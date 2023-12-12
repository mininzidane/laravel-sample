<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\GraphQL\Fields\EmailChartField;
use App\GraphQL\Fields\GenderField;
use App\GraphQL\Fields\HistoryChartField;
use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\LineItemField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\PatientAlertField;
use App\GraphQL\Fields\PatientAllergyField;
use App\GraphQL\Fields\PatientAllergyNoteField;
use App\GraphQL\Fields\PatientDrugAllergyField;
use App\GraphQL\Fields\PatientImageField;
use App\GraphQL\Fields\PhoneChartField;
use App\GraphQL\Fields\PrescriptionField;
use App\GraphQL\Fields\ProgressChartField;
use App\GraphQL\Fields\ReminderField;
use App\GraphQL\Fields\SaleField;
use App\GraphQL\Fields\SoapChartField;
use App\GraphQL\Fields\SpeciesField;
use App\GraphQL\Fields\TreatmentChartField;
use App\GraphQL\Fields\TreatmentSheetTreatmentField;
use App\GraphQL\Fields\VaccinationField;
use App\Models\Patient;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PatientGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "patient";

	protected $attributes = [
		"name" => "Patient",
		"description" => "A patient animal",
		"model" => Patient::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The first name of the patient",
				"alias" => "first_name",
			],
			"dateOfBirth" => [
				"type" => Type::string(),
				"description" => "When the patient was born",
				"alias" => "date_of_birth",
			],
			"dateOfDeath" => [
				"type" => Type::string(),
				"description" => "When the patient died",
				"alias" => "date_of_death",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "The species of the patient",
			],
			"speciesRelation" => (new SpeciesField([]))->toArray(),
			"breed" => [
				"type" => Type::string(),
				"description" => "The breed of the patient",
			],
			"marking" => [
				"type" => Type::string(),
				"description" => "The identifying markings of the patient",
			],
			"color" => [
				"type" => Type::string(),
				"description" => "The color of the patient",
			],
			"created" => [
				"type" => Type::string(),
				"description" => "When the patient was added",
				"alias" => "timestamp",
			],
			"notes" => [
				"type" => Type::string(),
				"description" => "Any additional notes about the patient",
			],
			"microchip" => [
				"type" => Type::string(),
				"description" => "The microchip associated with the patient",
			],
			"primary" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the owner is a primary owner.  Overlaps with the pivot table",
				"resolve" => function ($group) {
					return $group->pivot->primary;
				},
				"selectable" => false,
			],
			"percent" => [
				"type" => Type::string(),
				"description" =>
					"The percent ownership the owner has of the patient",
				"resolve" => function ($group) {
					return $group->pivot->percent;
				},
				"selectable" => false,
			],
			"relationship" => [
				"type" => Type::string(),
				"description" =>
					"The relationship between the owner and patient",
				"resolve" => function ($group) {
					return $group->pivot->relationship_type;
				},
				"selectable" => false,
			],
			"currentWeight" => [
				"type" => Type::string(),
				"description" => "The most recent chart recorded weight",
				"resolve" => function ($data, $args) {
					return $data->currentWeight;
				},
				"selectable" => false,
			],
			"aliasId" => [
				"type" => Type::string(),
				"description" => "Optional Alternate patient id",
				"alias" => "alias_id",
			],
			"owners" => [
				"type" => Type::listOf(
					GraphQL::type(OwnerGraphQLType::getGraphQLTypeName()),
				),
				"description" => "The owners of a patient",
				"resolve" => function ($data, $args) {
					return $data->owners()->get();
				},
			],
			"primaryOwner" => [
				"type" => GraphQL::type(OwnerGraphQLType::getGraphQLTypeName()),
				"description" => "The owners of a patient",
				"selectable" => false,
			],
			"primaryImage" => [
				"type" => Type::string(),
				"description" => "Single Image for patient",
			],
			"amountDue" => [
				"type" => Type::float(),
				"selectable" => false,
				"description" => "The current amount due for this patient",
			],
			"providers" => [
				"type" => Type::listOf(
					GraphQL::type(OwnerGraphQLType::getGraphQLTypeName()),
				),
				"description" => "The owners of a patient",
				"resolve" => function ($data, $args) {
					return $data->providers()->get();
				},
			],
			/* Simple Relationships */
			"gender_relation" => (new GenderField([
				"description" => "The current gender of the patient",
			]))->toArray(),
			"organization" => (new OrganizationField([
				"description" => "The associated organization",
			]))->toArray(),
			"preferredLocation" => new LocationField([
				"description" => "The preferred location of the patient",
			]),
			"invoices" => new InvoiceField([
				"isList" => true,
				"description" => "Invoices for the patient",
			]),
			"activeInvoice" => new InvoiceField([
				"description" => "Invoices for the patient",
				"query" => function ($args, $query, $ctx) {
					return $query
						->where("active", 1)
						->orderBy("updated_at", "desc")
						->take(1);
				},
			]),
			"appointments" => (new AppointmentField([
				"isList" => true,
				"description" => "Appointments for the patient",
			]))->toArray(),
			"reminders" => (new ReminderField([
				"isList" => true,
				"description" => "Reminders for the patient",
			]))->toArray(),
			"sales" => (new SaleField([
				"isList" => true,
				"description" => "Sales for the patient",
			]))->toArray(),
			"lineItems" => (new LineItemField([
				"isList" => true,
				"description" => "Line items that are tied to this patient",
			]))->toArray(),
			"soapCharts" => (new SoapChartField([
				"isList" => true,
				"description" => "Soap Charts associated with the patient",
				"args" => [
					"limit" => [
						"type" => Type::int(),
					],
					"signed" => [
						"type" => Type::boolean(),
					],
					"sort" => [
						"type" => Type::string(),
					],
				],
				"query" => function (array $args, $query, $ctx) {
					if (isset($args["signed"])) {
						$query->where("signed", $args["signed"]);
					}

					$limit = array_key_exists("limit", $args)
						? $args["limit"]
						: 10;
					if (isset($args["sort"]) && $args["sort"] === "desc") {
						$query->orderBy("date", "desc");
					}
					return $query->take($limit);
				},
			]))->toArray(),
			"historyCharts" => (new HistoryChartField([
				"isList" => true,
				"description" => "History Charts associated with the patient",
				"args" => [
					"limit" => [
						"type" => Type::int(),
					],
					"signed" => [
						"type" => Type::boolean(),
					],
					"sort" => [
						"type" => Type::string(),
					],
				],
				"query" => function (array $args, $query, $ctx) {
					if (isset($args["signed"])) {
						$query->where("signed", $args["signed"]);
					}

					$limit = array_key_exists("limit", $args)
						? $args["limit"]
						: 10;
					if (isset($args["sort"]) && $args["sort"] === "desc") {
						$query->orderBy("date", "desc");
					}
					return $query->take($limit);
				},
			]))->toArray(),
			"emailCharts" => (new EmailChartField([
				"isList" => true,
				"description" => "Email Charts associated with the patient",
				"args" => [
					"limit" => [
						"type" => Type::int(),
					],
					"signed" => [
						"type" => Type::boolean(),
					],
					"sort" => [
						"type" => Type::string(),
					],
				],
				"query" => function (array $args, $query, $ctx) {
					if (isset($args["signed"])) {
						$query->where("signed", $args["signed"]);
					}

					$limit = array_key_exists("limit", $args)
						? $args["limit"]
						: 10;
					if (isset($args["sort"]) && $args["sort"] === "desc") {
						$query->orderBy("date", "desc");
					}
					return $query->take($limit);
				},
			]))->toArray(),
			"progressCharts" => (new ProgressChartField([
				"isList" => true,
				"description" => "Progress Charts associated with the patient",
				"args" => [
					"limit" => [
						"type" => Type::int(),
					],
					"signed" => [
						"type" => Type::boolean(),
					],
					"sort" => [
						"type" => Type::string(),
					],
				],
				"query" => function (array $args, $query, $ctx) {
					if (isset($args["signed"])) {
						$query->where("signed", $args["signed"]);
					}

					$limit = array_key_exists("limit", $args)
						? $args["limit"]
						: 10;
					if (isset($args["sort"]) && $args["sort"] === "desc") {
						$query->orderBy("date", "desc");
					}
					return $query->take($limit);
				},
			]))->toArray(),
			"phoneCharts" => (new PhoneChartField([
				"isList" => true,
				"description" => "Phone Charts associated with the patient",
				"args" => [
					"limit" => [
						"type" => Type::int(),
					],
					"signed" => [
						"type" => Type::boolean(),
					],
					"sort" => [
						"type" => Type::string(),
					],
				],
				"query" => function (array $args, $query, $ctx) {
					if (isset($args["signed"])) {
						$query->where("signed", $args["signed"]);
					}

					$limit = array_key_exists("limit", $args)
						? $args["limit"]
						: 10;
					if (isset($args["sort"]) && $args["sort"] === "desc") {
						$query->orderBy("date", "desc");
					}
					return $query->take($limit);
				},
			]))->toArray(),
			"treatmentCharts" => (new TreatmentChartField([
				"isList" => true,
				"description" => "Treatment Charts associated with the patient",
				"args" => [
					"limit" => [
						"type" => Type::int(),
					],
					"signed" => [
						"type" => Type::boolean(),
					],
					"sort" => [
						"type" => Type::string(),
					],
				],
				"query" => function (array $args, $query, $ctx) {
					if (isset($args["signed"])) {
						$query->where("signed", $args["signed"]);
					}

					$limit = array_key_exists("limit", $args)
						? $args["limit"]
						: 10;
					if (isset($args["sort"]) && $args["sort"] === "desc") {
						$query->orderBy("date", "desc");
					}
					return $query->take($limit);
				},
			]))->toArray(),
			"prescriptions" => (new PrescriptionField([
				"isList" => true,
			]))->toArray(),
			"lastVet" => [
				"type" => Type::string(),
				"description" => "The name of the last doctor seen",
			],
			"vaccinations" => (new VaccinationField([
				"isList" => true,
			]))->toArray(),
			"images" => (new PatientImageField(["isList" => true]))->toArray(),
			"rabies" => [
				"type" => Type::boolean(),
				"selectable" => false,
			],
			"patientAlerts" => new PatientAlertField([
				"isList" => true,
				"description" => "Alerts for the patient",
			]),
			"patientAllergy" => new PatientAllergyField([
				"isList" => true,
				"description" => "Allergy for the patient",
			]),
			"patientDrugAllergy" => new PatientDrugAllergyField([
				"isList" => true,
				"description" => "Drug Allergy for the patient",
			]),
			"patientAllergyNote" => new PatientAllergyNoteField([
				"isList" => true,
				"description" => "Drug Allergy Note for the patient",
			]),
			"treatmentSheetTreatments" => (new TreatmentSheetTreatmentField([
				"isList" => true,
				"description" => "The associated treatment sheet treatments",
			]))->toArray(),
			"vcpContractId" => [
				"type" => Type::string(),
				"description" => "The color of the patient",
				"alias" => "vcp_contract_id",
				"selectable" => true,
			],
			"lastVisit" => [
				"type" => Type::string(),
				"description" => "Patients last visit",
			],
			"formattedAge" => [
				"type" => Type::string(),
				"description" => "Patients age",
			],
		];
	}
}
