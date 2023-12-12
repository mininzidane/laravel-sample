<?php

namespace App\GraphQL\Requests\Mutations\App\PatientVaccination;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Vaccination;
use App\Models\InvoiceStatus;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PatientVaccinationUpdateMutation extends PatientVaccinationMutation
{
	protected $model = Vaccination::class;

	// TODO:  Configure permission
	protected $permissionName = "Patient Vaccines: Update";

	protected $attributes = [
		"name" => "PatientVaccinationUpdate",
		"model" => Vaccination::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_VACCINATION_UPDATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.administeredBy.exists" => "A Valid User Must be Selected",
			"input.provider.exists" => "A Valid Provider Must be Selected",
			"input.location.exists" => "The Selected Location does not Exist",
			"input.location.required" => "A Valid Location Must be Selected",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("PatientVaccinationUpdateInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return mixed |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		// load vaccine from provided id
		$vaccination = Vaccination::on($this->subdomainName)
			->with(
				"provider",
				"administeredBy",
				"invoice",
				"invoiceItem",
				"item",
				"patient",
			)
			->findOrFail($this->args["input"]["id"]);

		// always
		if ($vaccination->itemKit) {
			throw new \Exception(
				"Cannot modify vaccine as it belongs to an item kit.",
				HippoGraphQLErrorCodes::VACCINE_CHANGE_ON_ITEM_KIT,
			);
		}

		$serialNumber = $this->fetchInput("serialNumber", null);
		$dosage = $this->fetchInput("dosage", 1);
		$providerId = $this->fetchInput("provider", null);
		$administeredBy = $this->fetchInput("administeredBy", null);
		$administeredAt = $this->fetchInput("administeredDate", null);
		$lotNumber = $this->fetchInput("lotNumber", null);
		$expirationDate = $this->fetchInput("expirationDate", null);
		$processReminders = $this->fetchInput("processReminders", false);
		$invoiceId = $this->fetchInput("invoice", null);
		$locationId = $this->fetchInput("location", null);
		$administeredLocationId = $this->fetchInput(
			"administeredLocationId",
			null,
		);
		$allowExcessiveQuantity = $this->fetchInput(
			"allowExcessiveQuantity",
			false,
		);

		if (
			is_null($locationId) &&
			!(is_null($invoiceId) || $invoiceId == -1)
		) {
			throw new \Exception(
				"Location is required if assigning the vaccine to an invoice",
				HippoGraphQLErrorCodes::VACCINE_LOCATION_REQUIRED_FOR_INVOICE,
			);
		}

		$this->updateVaccinationInvoice(
			$invoiceId,
			$vaccination,
			$locationId,
			$processReminders,
			$serialNumber,
		);

		$vaccination->refresh();

		if (
			!$vaccination->provider ||
			$vaccination->provider->id !== $providerId
		) {
			$this->prepareProvider($providerId, $vaccination->item);

			$vaccination->seen_by = $providerId;
		}

		if ($vaccination->invoiceItem()->exists()) {
			if (
				$serialNumber &&
				in_array($vaccination->invoice->status_id, [
					InvoiceStatus::OPEN,
					InvoiceStatus::ESTIMATE,
				])
			) {
				$vaccination->invoiceItem->serial_number = $serialNumber;
			}

			// There is a two-way binding with the Invoice Item's administered_at and the Vaccination's administered_at.
			$vaccination->invoiceItem->administered_date = $administeredAt;
			$vaccination->invoiceItem->save();

			$this->handleInvoiceItemQuantityModifications(
				$vaccination->invoiceItem,
				$dosage,
				$allowExcessiveQuantity,
			);
		}

		$vaccination->fill([
			"administered_by" => $administeredBy,
			"administered_date" => $administeredAt,
			"receiving_item_lot_number" => $lotNumber,
			"receiving_item_expiration_date" => $expirationDate,
			"location_administered" => $administeredLocationId,
			"serialnumber" => $serialNumber,
			"dosage" => $dosage,
		]);

		if ($vaccination->isDirty()) {
			$vaccination->push();

			if ($vaccination->invoice()->exists()) {
				$this->reprocessDiscountsTaxesAndTotals($vaccination->invoice);
			}
		}

		$this->affectedId = $vaccination->id;

		return Vaccination::on($this->subdomainName)
			->where("client_id", $this->args["input"]["id"])
			->paginate(1);
	}

	protected function updateVaccinationInvoice(
		$invoiceId,
		$vaccination,
		$locationId,
		$processReminders = true,
		$serialNumber = ""
	) {
		if (
			$invoiceId === null ||
			($vaccination->invoice()->exists() &&
				$vaccination->invoice->id === $invoiceId)
		) {
			return;
		}

		if ($invoiceId === -1) {
			if (!$vaccination->invoice()->exists()) {
				return;
			}

			// If there is an invoice to remove, do so
			$this->removeInvoiceFromVaccination($vaccination);
			return;
		}

		if ($invoiceId === 0) {
			$targetInvoice = $this->createInvoiceForPatient(
				$vaccination->patient,
				$locationId,
			);
		} else {
			$targetInvoice = $this->fetchOpenInvoice($invoiceId);
		}

		if ($vaccination->invoice()->exists()) {
			$this->changeInvoiceOnVaccination($targetInvoice, $vaccination);
		} else {
			$this->addInvoiceToVaccination(
				$targetInvoice,
				$vaccination,
				$processReminders,
				$serialNumber,
			);
		}

		$vaccination->push();
	}

	protected function addInvoiceToVaccination(
		$targetInvoice,
		$vaccination,
		$processReminders = true,
		$serialNumber = ""
	) {
		$newInvoiceItem = $this->createSingleInvoiceItemRecord(
			$targetInvoice,
			$vaccination->item,
			$vaccination->dosage,
			true,
			null,
			$vaccination->provider,
			$vaccination->administered_date ??
				Carbon::now(
					$targetInvoice->location->tz->php_supported,
				)->toDateString(),
			$processReminders,
			null,
			null,
			$serialNumber,
		);

		$vaccination->invoice_id = $targetInvoice->id;
		$vaccination->invoice_item_id = $newInvoiceItem->id;
	}

	protected function removeInvoiceFromVaccination($vaccination)
	{
		$this->deleteInvoiceItem($vaccination->invoiceItem);
		$vaccination->invoice_id = null;
	}

	protected function changeInvoiceOnVaccination($targetInvoice, $vaccination)
	{
		$originalInvoice = $vaccination->invoice;

		if (
			$originalInvoice->status &&
			$originalInvoice->status->name === "Completed"
		) {
			throw new \Exception(
				"Cannot change the invoice since it has already been completed",
				HippoGraphQLErrorCodes::VACCINE_CHANGE_ON_COMPLETED_INVOICE,
			);
		}

		$vaccination->invoiceItem->invoice_id = $targetInvoice->id;

		foreach ($vaccination->invoiceItem->reminders as $reminder) {
			$reminder->invoice_id = $targetInvoice->id;
		}

		$vaccination->invoice_id = $targetInvoice->id;

		$this->reprocessDiscountsTaxesAndTotals($originalInvoice);
	}
}
