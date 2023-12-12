<?php

namespace App\GraphQL\Types;

use App\Models\DrugAllergy;
use GraphQL\Type\Definition\Type;

class DrugAllergyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "drugAllergies";

	protected $attributes = [
		"name" => "DrugAllergy",
		"description" => "Drug Allergies names",
		"model" => DrugAllergy::class,
	];

	public function columns(): array
	{
		return [
			"name" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The name of the drug allergy",
			],
		];
	}
}
