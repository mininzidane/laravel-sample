<?php

namespace App\GraphQL\Requests\Mutations\App\PatientVaccination;

use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\InvoiceItem\InvoiceItemAddMutation;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\ItemType;
use App\Models\Vaccination;

abstract class PatientVaccinationMutation extends InvoiceItemAddMutation
{
	protected $model = Vaccination::class;

	protected $attributes = [
		"name" => "PatientVaccination",
		"model" => Vaccination::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_VACCINATION_CREATE;

	protected function requireVaccine($item)
	{
		if (!$item->is_vaccine) {
			throw new \Exception(
				"The item applied is not a vaccine",
				HippoGraphQLErrorCodes::VACCINE_NOT_PROVIDED,
			);
		}
	}

	protected function createInvoiceForPatient($patient, $locationId)
	{
		Invoice::on($this->subdomainName)
			->with("itemKitItems")
			->where("patient_id", $patient->id)
			->update(["active" => 0]);

		$openStatus = InvoiceStatus::on($this->subdomainName)
			->where("name", "Open")
			->firstOrFail();

		return Invoice::on($this->subdomainName)->create([
			"patient_id" => $patient->id,
			"owner_id" => $patient->primaryOwner->id,
			"location_id" => $locationId,
			"user_id" => $this->guard()->user()->id,
			"active" => 1,
			"status_id" => $openStatus->id,
		]);
	}

	protected function fetchOpenInvoice($invoiceId)
	{
		$invoice = Invoice::on($this->subdomainName)->find($invoiceId);

		if ($invoice && $invoice->invoiceStatus->name === "Complete") {
			throw new \Exception(
				"Cannot add a vaccine to a completed invoice",
				HippoGraphQLErrorCodes::VACCINE_ON_COMPLETED_INVOICE,
			);
		}

		return $invoice;
	}

	protected function createVaccinationsForItemKit(
		$vaccinationDetails,
		$invoice,
		$item,
		$chart = null,
		$provider = null,
		$processReminders = true
	) {
		if (!$processReminders) {
			$processItemKitReminders = false;
			$processItemKitItemReminders = false;
		} else {
			if ($item->reminders) {
				$processItemKitReminders = true;
				$processItemKitItemReminders = false;
			} else {
				$processItemKitReminders = false;
				$processItemKitItemReminders = true;
			}
		}

		// Create item kit invoice item
		$result = $this->createVaccinationForSingleItem(
			$vaccinationDetails,
			$invoice,
			$item,
			1,
			true,
			$chart,
			$provider,
			$processItemKitReminders,
		);

		$invoiceItemKitID = $result["invoiceItem"]->id ?? null;

		// Create an invoice item for each item kit item
		foreach ($item->itemKitItems as $itemKitItem) {
			$this->createVaccinationForSingleItem(
				$vaccinationDetails,
				$invoice,
				$itemKitItem->item,
				$itemKitItem->quantity,
				true,
				$chart,
				$provider,
				$processItemKitItemReminders,
				null,
				$invoiceItemKitID,
			);
		}
	}

	protected function createVaccinationForSingleItem(
		$vaccinationDetails,
		$invoice,
		$item,
		$quantity,
		$allowExcessiveQuantity = false,
		$chart = null,
		$provider = null,
		$processReminders = true,
		$serialNumber = null,
		$kitInvoiceItemID = null
	) {
		if ($invoice) {
			$newInvoiceItem = $this->createSingleInvoiceItemRecord(
				$invoice,
				$item,
				$quantity,
				$allowExcessiveQuantity,
				$chart,
				$provider,
				$vaccinationDetails["administered_date"] ?? null,
				$processReminders,
				null,
				null,
				$serialNumber,
				$kitInvoiceItemID,
			);
		} else {
			$newInvoiceItem = null;
		}

		if ($item->type_id !== ItemType::ITEM_KIT && $item->is_vaccine) {
			$newVaccination = $this->createPatientVaccinationRecord(
				$vaccinationDetails,
				$item,
			);
		} else {
			$newVaccination = null;
		}

		if ($newVaccination && $newInvoiceItem) {
			$this->associatePatientVaccinationWithInvoiceItem(
				$newVaccination,
				$newInvoiceItem,
			);
		}

		return [
			"vaccine" => $newVaccination,
			"invoiceItem" => $newInvoiceItem,
		];
	}

	protected function createPatientVaccinationRecord(
		$vaccinationDetails,
		$item,
		$itemKit = null
	) {
		$vaccinationDetails["vaccine_name"] = $item->name;
		$vaccinationDetails["vaccine_item_id"] = $item->id;

		if ($itemKit) {
			$vaccinationDetails["item_kit_id"] = $itemKit->id;
		}

		return Vaccination::on($this->subdomainName)->create(
			$vaccinationDetails,
		);
	}

	protected function associatePatientVaccinationWithInvoiceItem(
		$vaccination,
		$invoiceItem
	) {
		$vaccination->invoice_id = $invoiceItem->invoice->id;
		$vaccination->invoice_item_id = $invoiceItem->id;

		$vaccination->reminder_id =
			$invoiceItem->reminders->count() > 0
				? $invoiceItem->reminders[0]->id
				: null;

		if ($invoiceItem->has_inventory) {
			$vaccination->receiving_item_lot_number =
				$invoiceItem->inventoryTransactions[0]->inventory->lot_number;
			$vaccination->receiving_item_expiration_date =
				$invoiceItem->inventoryTransactions[0]->inventory->expiration_date;
			$vaccination->manufacturer_supplier =
				$invoiceItem->inventoryTransactions[0]->inventory->receivingItem
					->receiving->supplier->id ?? null;
		}

		$vaccination->save();
	}

	protected function useNextTagNumber($item)
	{
		$nextTag = $item->next_tag_number;

		if ($nextTag) {
			$item->update([
				"next_tag_number" => $nextTag + 1,
			]);
		}

		return $nextTag;
	}
}
