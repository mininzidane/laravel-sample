<?php

namespace App\GraphQL\Requests\Mutations\App\InvoiceItem;

use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Gender;
use App\Models\Inventory;
use App\Models\InventoryStatus;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Reminder;
use App\Models\ItemType;
use DateInterval;
use DateTime;
use Illuminate\Support\Carbon;

class InvoiceItemAddMutation extends InvoiceItemMutation
{
	protected $model = InvoiceItem::class;

	public function createSingleInvoiceItemRecord(
		$invoice,
		$item,
		$requestedQuantity,
		$allowExcessiveQuantity,
		$chart,
		$provider,
		$administeredDate = null,
		$processReminders = true,
		$processReproductive = true,
		$processEuthanasia = true,
		$serialNumber = null,
		$kitInvoiceItemID = null
	) {
		if (!in_array($invoice->invoiceStatus->name, ["Open", "Estimate"])) {
			throw new \Exception(
				"The selected invoice cannot be modified as it is no longer open or an estimate.",
				HippoGraphQLErrorCodes::INVOICE_ADD_ITEM_INVALID_INVOICE_STATUS,
			);
		}

		if ($item->hasInventory && !$allowExcessiveQuantity) {
			$this->checkRemainingQuantity(
				$requestedQuantity,
				$item,
				$invoice->location,
			);
		}

		$invoiceItem = $this->createNewInvoiceItem(
			$invoice,
			$item,
			$requestedQuantity,
			$provider,
			$chart,
			$serialNumber,
			$kitInvoiceItemID,
			$administeredDate,
		);

		$this->finalizeInvoiceItemAdd(
			$invoiceItem,
			$invoice->patient,
			$invoice->invoiceStatus->name == "Estimate"
				? false
				: $processReminders,
			$invoice->invoiceStatus->name == "Estimate"
				? false
				: $processReproductive,
			$invoice->invoiceStatus->name == "Estimate"
				? false
				: $processEuthanasia,
		);

		// don't add transaction for estimate
		if (
			$invoiceItem->hasInventory &&
			$invoice->invoiceStatus->name != "Estimate"
		) {
			$this->createTransactionsForDispensedQuantity(
				$invoice,
				$requestedQuantity,
				$invoiceItem,
				$allowExcessiveQuantity,
			);
		}

		return $invoiceItem;
	}

	public function createNewInvoiceItem(
		$invoice,
		$item,
		$requestedQuantity,
		$provider,
		$chart = null,
		$serialNumber = null,
		$kitInvoiceItemID = null,
		$administeredDate = null
	) {
		$invoice->load("invoiceItems");
		$item->load("itemTaxes", "itemKitItems", "itemVolumePricing");

		$currentMaxLine = $invoice->invoiceItems->max("line");
		$nextLine = $currentMaxLine > 0 ? $currentMaxLine + 1 : 1;

		$volumePrice = $this->getVolumePrice($item, $requestedQuantity);

		$newInvoiceItemDetails = [
			"quantity" => $requestedQuantity,
			"name" => $item->name,
			"number" => $item->number,
			"line" => $nextLine,
			"price" => $item->unit_price,
			"discount_percent" => 0,
			"discount_amount" => 0,
			"total" => 0,
			"description" => $item->description,
			"allow_alt_description" => $item->allow_alt_description,
			"cost_price" => $item->cost_price,
			"volume_price" => $volumePrice->unit_price ?? null,
			"volume_quantity" => $volumePrice->quantity ?? null,
			"apply_discount_to_remainder" => $item->apply_discount_to_remainder,
			"markup_percentage" => $item->markup_percentage,
			"unit_price" => $item->unit_price,
			"minimum_sale_amount" => $item->minimum_sale_amount,
			"dispensing_fee" => $item->dispensing_fee,
			"is_vaccine" => $item->is_vaccine,
			"is_prescription" => $item->is_prescription,
			"is_serialized" => $item->is_serialized,
			"is_controlled_substance" => $item->is_controlled_substance,
			"is_euthanasia" => $item->is_euthanasia,
			"is_reproductive" => $item->is_reproductive,
			"hide_from_register" => $item->hide_from_register,
			"requires_provider" => $item->requires_provider,
			"is_in_wellness_plan" => $item->is_in_wellness_plan,
			"vcp_item_id" => $item->vcp_item_id,
			"drug_identifier" => $item->drug_identifier,
			"belongs_to_kit_id" => $kitInvoiceItemID,
			"is_single_line_kit" => $item->is_single_line_kit,
			"item_id" => $item->id,
			"serial_number" => $serialNumber,
			"administered_date" => $administeredDate,
		];

		$newInvoiceItem = $invoice
			->invoiceItems()
			->create($newInvoiceItemDetails);

		if ($chart) {
			$newInvoiceItem->chart()->associate($chart);
		}

		if ($provider) {
			$newInvoiceItem->provider()->associate($provider);
		}

		$newInvoiceItem->itemType()->associate($item->itemType);
		$newInvoiceItem->itemCategory()->associate($item->itemCategory);

		if ($invoice->is_taxable) {
			$this->createTaxes($item, $newInvoiceItem);
		}

		$newInvoiceItem->save();

		return $newInvoiceItem;
	}

	public function checkRemainingQuantity($requestedQuantity, $item, $location)
	{
		if (!$item->hasInventory) {
			return true;
		}

		$inventoryCompleteStatus = InventoryStatus::on($this->subdomainName)
			->where("name", "Complete")
			->firstOrFail();

		$remainingQuantity = Inventory::on($this->subdomainName)
			->where("status_id", $inventoryCompleteStatus->id)
			->where("item_id", $item->id)
			->where("location_id", $location->id)
			->sum("remaining_quantity");

		if ($requestedQuantity > $remainingQuantity) {
			throw new \Exception(
				"The requested quantity of " .
					$requestedQuantity .
					" exceeds the remaining " .
					$remainingQuantity .
					" in inventory.",
				HippoGraphQLErrorCodes::INVOICE_ADD_ITEM_EXCEEDS_INVENTORY,
			);
		}
	}

	public function finalizeInvoiceItemAdd(
		$invoiceItem,
		$patient,
		$processReminders = true,
		$processReproductive = true,
		$processEuthanasia = true
	) {
		if ($processReproductive) {
			$this->finalizeInvoiceItemAddReproductive($invoiceItem, $patient);
		}

		if ($processEuthanasia) {
			$this->finalizeInvoiceItemAddEuthanasia($invoiceItem, $patient);
		}

		if ($processReminders) {
			$this->finalizeInvoiceItemAddReminders($invoiceItem, $patient);
		}
	}

	protected function finalizeInvoiceItemAddReproductive(
		$invoiceItem,
		$patient
	) {
		if (
			!$invoiceItem->is_reproductive ||
			!$patient->gender_relation ||
			$patient->gender_relation->neutered
		) {
			return;
		}

		$newGender = Gender::on($this->subdomainName)
			->where("species", $patient->gender_relation->species)
			->where("sex", $patient->gender_relation->sex)
			->where("neutered", 1)
			->firstOrFail();

		$patient->gender_relation()->associate($newGender);
		$patient->save();
	}

	protected function finalizeInvoiceItemAddEuthanasia($invoiceItem, $patient)
	{
		if (!$invoiceItem->is_euthanasia || $patient->deceased) {
			return;
		}

		$patient->date_of_death = Carbon::now();
		$patient->save();
	}

	protected function finalizeInvoiceItemAddReminders($invoiceItem, $patient)
	{
		if ($patient->deceased) {
			return;
		}

		// remove any reminders for other invoices with the same invoice item and patient
		$existingItemReminders = Reminder::on($this->subdomainName)
			->where("client_id", $patient->id)
			->where("invoice_id", "<>", $invoiceItem->invoice->id)
			->where("item_id", $invoiceItem->item->id)
			->get();

		if ($existingItemReminders) {
			foreach ($existingItemReminders as $existingItemReminder) {
				$existingItemReminder->removed = 1;
				$existingItemReminder->save();
				$existingItemReminder->delete();
			}
		}

		$invoiceItem->load("item.reminderReplaces");

		// when an invoice item has an associated item that meets the conditions to remove or replace a reminder, mark the previous reminder removed
		if (sizeof($invoiceItem->item->reminderReplaces) > 0) {
			foreach ($invoiceItem->item->reminderReplaces as $reminderReplace) {
				$replacedItemId = $reminderReplace->replacedItem->id;

				$toBeReplacedReminders = Reminder::on($this->subdomainName)
					->where("item_id", $replacedItemId)
					->where("client_id", $patient->id)
					->get();

				foreach ($toBeReplacedReminders as $toBeReplacedReminder) {
					$toBeReplacedReminder->removed = 1;
					$toBeReplacedReminder->removed_by_item_id =
						$invoiceItem->item->id;
					$toBeReplacedReminder->removed_datetime = Carbon::now();
					$toBeReplacedReminder->save();
					$toBeReplacedReminder->delete();
				}
			}
		}

		// return: if a reminder already exists for this invoice item for this patient
		$alreadyCreatedReminders = Reminder::on($this->subdomainName)
			->where("item_id", $invoiceItem->item->id)
			->where("invoice_id", $invoiceItem->invoice->id)
			->where("client_id", $patient->id)
			->get();

		if (sizeof($alreadyCreatedReminders) > 0) {
			return;
		}

		if (!$invoiceItem->item->reminderInterval) {
			return;
		}

		$now = new DateTime();
		$startDate = $now->format("Y-m-d");
		$dueDate = $this->calculateDueDateFromFrequency(
			$now,
			$invoiceItem->item->reminderInterval->code,
		);

		$reminderDescription = $invoiceItem->name;

		$newReminderDetails = [
			"organization_id" =>
				$invoiceItem->invoice->location->organization->id,
			"location_id" => $invoiceItem->invoice->location->id,
			"client_id" => $patient->id,
			"item_id" => $invoiceItem->item->id,
			"sale_id" => null,
			"invoice_id" => $invoiceItem->invoice->id,
			"invoice_item_id" => $invoiceItem->id,
			"description" => $reminderDescription,
			"frequency" => $invoiceItem->item->reminderInterval->code,
			"start_date" => $startDate,
			"due_date" => $dueDate,
		];

		// create a new reminder with the reminder details
		Reminder::on($this->subdomainName)->create($newReminderDetails);
	}

	public function calculateDueDateFromFrequency(DateTime $date, $frequency)
	{
		return $date->add(new DateInterval($frequency))->format("Y-m-d");
	}

	public function createTaxes($item, $invoiceItem)
	{
		// Won't add taxes if there are no associated taxes
		foreach ($item->itemTaxes as $itemTax) {
			if ($itemTax->deletedAt) {
				continue;
			}

			InvoiceItemTax::on($this->subdomainName)->create([
				"invoice_item_id" => $invoiceItem->id,
				"tax_id" => $itemTax->tax->id,
				"name" => $itemTax->tax->name,
				"percent" => $itemTax->tax->percent,
				"amount" => 0,
			]);
		}
	}

	protected function createInvoiceItemsForItemKit(
		$invoice,
		$item,
		$chart = null,
		$provider = null,
		$administeredDate = null
	) {
		// Create item kit invoice item
		$newInvoiceItem = $this->createSingleInvoiceItemRecord(
			$invoice,
			$item,
			1,
			true,
			$chart,
			$provider,
			$administeredDate,
		);

		$invoiceItemIds[] = $newInvoiceItem->id;
		$kitInvoiceItemID = $newInvoiceItem->id;

		$item->load("itemKitItems");

		// Create an invoice item for each item kit item
		foreach ($item->itemKitItems as $itemKitItem) {
			$newInvoiceItem = $this->createSingleInvoiceItemRecord(
				$invoice,
				$itemKitItem->item,
				$itemKitItem->quantity,
				true,
				$chart,
				$provider,
				$administeredDate,
				true,
				true,
				true,
				null,
				$kitInvoiceItemID,
			);

			$invoiceItemIds[] = $newInvoiceItem->id;
		}

		return $invoiceItemIds;
	}

	protected function createInvoiceItemForSingleItem(
		$invoice,
		$item,
		$chart = null,
		$provider = null,
		$administeredDate = null
	) {
		$requestedQuantity = $this->args["input"]["quantity"];

		$allowExcessiveQuantity = array_key_exists(
			"allowExcessiveQuantity",
			$this->args["input"],
		)
			? $this->args["input"]["allowExcessiveQuantity"]
			: false;

		$newInvoiceItem = $this->createSingleInvoiceItemRecord(
			$invoice,
			$item,
			$requestedQuantity,
			$allowExcessiveQuantity,
			$chart,
			$provider,
			$administeredDate,
		);

		$invoiceItemIds[] = $newInvoiceItem->id;

		return $invoiceItemIds;
	}
}
