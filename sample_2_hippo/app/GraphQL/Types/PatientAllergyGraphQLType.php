<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\PatientDrugAllergyField;
use App\GraphQL\Fields\PatientField;
use App\Models\PatientAllergy;
use GraphQL\Type\Definition\Type;

class PatientAllergyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "patientAllergies";

	protected $attributes = [
		"name" => "PatientAllergy",
		"description" => "Allergies for patients",
		"model" => PatientAllergy::class,
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

			"patient" => (new PatientField([
				"description" => "The patient associated with the appointment",
			]))->toArray(),

			"patientDrugAllergy" => (new PatientDrugAllergyField([
				"description" => "The patient associated with the appointment",
			]))->toArray(),

			"allergy" => [
				"type" => Type::string(),
				"description" => "Description of allergy",
			],

			"removed" => [
				"type" => Type::boolean(),
				"description" => "Has the alert been removed",
			],
		];
	}
}
