<?php

namespace App\GraphQL\Types;

use App\Models\PatientAllergyNote;
use GraphQL\Type\Definition\Type;

class PatientAllergyNoteGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "patientAllergyNote";

	protected $attributes = [
		"name" => "PatientAllergyNote",
		"description" => "Note for allergies for patients",
		"model" => PatientAllergyNote::class,
	];

	public function columns(): array
	{
		return [
			"clientId" => [
				"type" => Type::int(),
				"description" => "The id of the alert",
				"alias" => "client_id",
			],

			"note" => [
				"type" => Type::string(),
				"description" => "Note for allergy",
			],
		];
	}
}
