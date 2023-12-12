<?php

namespace App\GraphQL\InputObjects\PatientVaccination;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\VaccinationGraphQLType;
use GraphQL\Type\Definition\Type;

class PatientVaccinationCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "patientVaccinationCreateInput",
		"description" => "The input object for creating a new vaccination",
	];

	protected $graphQLType = VaccinationGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"patient" => [
				"type" => Type::int(),
				"description" => "The id of the patient to be vaccinated",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Patient,id",
				],
			],
			"vaccine" => [
				"type" => Type::int(),
				"description" => "The id of the vaccine item",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Item,id",
				],
			],
			"invoice" => [
				"type" => Type::int(),
				"description" =>
					"The id of the invoice to assign to this vaccination",
				"default" => null,
			],
			"administeredBy" => [
				"type" => Type::int(),
				"description" =>
					"The id of the provider that administered this vaccine",
				"default" => null,
			],
			"administeredDate" => [
				"type" => Type::string(),
				"description" => "The date the vaccine was administered",
				"rules" => ["date"],
			],
			"chart" => [
				"type" => Type::int(),
				"description" =>
					"The id of the chart to assign to this vaccine",
				"default" => null,
			],
			"chartType" => [
				"type" => Type::string(),
				"description" => "The type of chart to be associated",
				"default" => null,
			],
			"provider" => [
				"type" => Type::int(),
				"description" =>
					"The id of the provider that prescribed this vaccine",
				"rules" => ["exists:" . $subdomainName . "App\Models\User,id"],
				"default" => null,
			],
			"location" => [
				"type" => Type::int(),
				"description" =>
					"The id of the current location where this vaccine was added, for administeredDate timezone",
				"default" => null,
			],
			"administeredLocationId" => [
				"type" => Type::int(),
				"description" =>
					"The id of the location where this vaccine was administered",
				"default" => null,
			],
			"dosage" => [
				"type" => Type::int(),
				"description" => "The dosage of this vaccination",
				"default" => 1,
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" => "The serial number of the vaccine used",
				"default" => null,
				"rules" => ["string"],
			],
			"processReminders" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not reminders will be added for follow-ups",
				"default" => 0,
			],
			"allowExcessiveQuantity" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not quantities that would result in negative totals are allowed",
				"default" => 0,
			],
		];
	}
}
