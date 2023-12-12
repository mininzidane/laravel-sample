<?php

namespace App\GraphQL\Types;

use App\Models\PatientDrugAllergy;
use GraphQL\Type\Definition\Type;

class PatientDrugAllergyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "patientDrugAllergy";

	protected $attributes = [
		"name" => "PatientDrugAllergy",
		"description" => "Drug allergies for patients",
		"model" => PatientDrugAllergy::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the alert",
				"alias" => "id",
			],
			"clientId" => [
				"type" => Type::int(),
				"description" => "The id of the alert",
				"alias" => "client_id",
			],

			"allergy" => [
				"type" => Type::string(),
				"description" => "Description of allergy",
			],

			"removed" => [
				"type" => Type::int(),
				"description" => "Has the alert been removed",
			],
		];
	}
}
