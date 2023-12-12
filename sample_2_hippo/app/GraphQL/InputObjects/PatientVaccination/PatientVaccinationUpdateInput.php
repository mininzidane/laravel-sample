<?php

namespace App\GraphQL\InputObjects\PatientVaccination;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\VaccinationGraphQLType;
use GraphQL\Type\Definition\Type;

class PatientVaccinationUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "patientVaccinationUpdateInput",
		"description" => "The input object for creating a new vaccination",
	];

	protected $graphQLType = VaccinationGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"id" => [
				"type" => Type::int(),
				"description" => "The id of the vaccination to update",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Vaccination,id",
				],
			],
			"invoice" => [
				"type" => Type::int(),
				"description" =>
					"The id of the invoice to assign to this vaccination",
				"default" => null,
			],
			"lotNumber" => [
				"type" => Type::string(),
				"description" => "The lot number of the vaccine",
				"default" => null,
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" => "The expiration date of the vaccine",
				"rules" => ["date"],
				"default" => null,
			],
			"administeredBy" => [
				"type" => Type::int(),
				"description" =>
					"The id of the provider that administered this vaccine",
				"rules" => ["exists:" . $subdomainName . "App\Models\User,id"],
				"default" => null,
			],
			"administeredDate" => [
				"type" => Type::string(),
				"description" => "The date the vaccine was administered",
				"rules" => ["date"],
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
					"The id of the current location where this vaccine was added",
				"rules" => [
					"exists:" . $subdomainName . "App\Models\Location,id",
				],
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
				"description" => "The quantity of this invoice item",
				"default" => 1,
				"rules" => ["numeric"],
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
