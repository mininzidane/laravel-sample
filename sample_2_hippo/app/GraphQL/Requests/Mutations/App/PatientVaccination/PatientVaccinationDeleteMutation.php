<?php

namespace App\GraphQL\Requests\Mutations\App\PatientVaccination;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\Vaccination;
use App\Models\InvoiceStatus;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class PatientVaccinationDeleteMutation extends PatientVaccinationMutation
{
	protected $model = Vaccination::class;

	protected $permissionName = "Patient Vaccines: Delete";

	protected $attributes = [
		"name" => "PatientVaccinationDelete",
		"model" => Vaccination::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_VACCINATION_DELETE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"id.required" => "A Valid Vaccine Must be Selected",
			"id.exists" => "A Valid User Must be Selected",
		];
	}

	public function args(): array
	{
		$subdomainName = request()->header("Subdomain");

		return [
			"id" => [
				"name" => "id",
				"type" => Type::int(),
				"rules" => [
					"required",
					"exists:" . $subdomainName . ".App\Models\Vaccination,id",
				],
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
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$vaccinationToDelete = Vaccination::on(
			$this->subdomainName,
		)->findOrFail($this->args["id"]);

		// Check if the associated invoice item exists and isn't soft-deleted before trying to modify it.
		if (
			$vaccinationToDelete->invoiceItem &&
			!$vaccinationToDelete->invoiceItem->trashed() &&
			$vaccinationToDelete->invoice &&
			in_array($vaccinationToDelete->invoice->status_id, [
				InvoiceStatus::OPEN,
				InvoiceStatus::ESTIMATE,
			])
		) {
			$this->deleteInvoiceItem($vaccinationToDelete->invoice_item_id);
		}

		$patientId = $vaccinationToDelete->patient->id;

		$this->affectedId = $this->args["id"];

		$vaccinationToDelete->delete();

		return Vaccination::on($this->subdomainName)
			->where("client_id", $patientId)
			->paginate(1);
	}
}
