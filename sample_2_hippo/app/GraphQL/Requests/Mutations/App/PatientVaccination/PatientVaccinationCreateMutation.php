<?php

namespace App\GraphQL\Requests\Mutations\App\PatientVaccination;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Item;
use App\Models\Location;
use App\Models\Patient;
use App\Models\Vaccination;
use App\Models\ItemType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PatientVaccinationCreateMutation extends PatientVaccinationMutation
{
	protected $model = Vaccination::class;

	protected $permissionName = "Patient Vaccines: Create";

	protected $attributes = [
		"name" => "PatientVaccinationCreate",
		"model" => Vaccination::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_VACCINATION_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.vaccine.exists" => "A Valid Vaccine Must be Selected",
			"input.administeredBy.exists" => "A Valid User Must be Selected",
			"input.chart.exists" => "A Valid Chart Must be Selected",
			"input.provider.exists" => "A Valid Provider Must be Selected",
			"input.location.exists" => "The Selected Location does not Exist",
			"input.location.required" => "A Valid Location Must be Selected",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("PatientVaccinationCreateInput"),
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
		$item = Item::on($this->subdomainName)
			->with("itemType", "category", "taxes")
			->findOrFail($this->args["input"]["vaccine"]);

		$this->requireVaccine($item);

		$invoiceId = $this->fetchInput("invoice", -1);
		$requestedQuantity = $this->fetchInput("dosage", 1);
		$serialNumber = $this->fetchInput("serialNumber", null);
		$processReminders = $this->fetchInput("processReminders", false);
		$allowExcessiveQuantity = $this->fetchInput(
			"allowExcessiveQuantity",
			false,
		);
		$patientId = $this->fetchInput("patient", null);
		$locationId = $this->fetchInput("location", null);
		$providerId = $this->fetchInput("provider", null);
		$administeredLocationId = $this->fetchInput(
			"administeredLocationId",
			null,
		);

		$chart = $this->prepareChart(
			$this->fetchInput("chartType", ""),
			$this->fetchInput("chart", 0),
		);

		$provider = $this->prepareProvider($providerId, $item);

		$patient = Patient::on($this->subdomainName)
			->with("owners")
			->findOrFail($patientId);

		$this->processSpeciesRestrictions($patient, $item);

		if ($invoiceId === 0) {
			$invoice = $this->createInvoiceForPatient($patient, $locationId);
		} elseif ($invoiceId !== -1) {
			$invoice = $this->fetchOpenInvoice($invoiceId);
		} else {
			$invoice = null;
		}

		if ($item->type_id === ItemType::RABIES_TAG && !$serialNumber) {
			$serialNumber = $this->useNextTagNumber($item);
		}

		$location = Location::on($this->subdomainName)
			->where("id", $locationId)
			->first();

		$administeredDate = Carbon::now(
			$location->tz->php_supported,
		)->toDateString();

		if (array_key_exists("administeredDate", $this->args["input"])) {
			$administeredDate = Carbon::parse(
				$this->args["input"]["administeredDate"],
			)->toDateString();
		}

		$vaccinationDetails = [
			"client_id" => $patient->id,
			"current_owner_id" => $patient->primaryOwner->id,
			"current_weight" => $patient->currentWeight,
			"current_gender" => $patient->gender_relation->gender ?? null,
			"seen_by" => $providerId,
			"dosage" => $requestedQuantity,
			"administered_by" => $this->fetchInput("administeredBy", null),
			"administered_date" => $administeredDate,
			"location_administered" => $administeredLocationId,
			"serialnumber" => $serialNumber,
		];

		if ($item->type_id === ItemType::ITEM_KIT) {
			$this->createVaccinationsForItemKit(
				$vaccinationDetails,
				$invoice,
				$item,
				$chart,
				$provider,
				$processReminders,
			);
		} else {
			$this->createVaccinationForSingleItem(
				$vaccinationDetails,
				$invoice,
				$item,
				$requestedQuantity,
				$allowExcessiveQuantity,
				$chart,
				$provider,
				$processReminders,
				$serialNumber,
			);
		}

		if ($invoice) {
			$this->reprocessDiscountsTaxesAndTotals($invoice);
		}

		$this->affectedId = $patient->id;

		return Vaccination::on($this->subdomainName)
			->where("client_id", $patient->id)
			->orderBy("created_at", "desc")
			->paginate(1);
	}
}
