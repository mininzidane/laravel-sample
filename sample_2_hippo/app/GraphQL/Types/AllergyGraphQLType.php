<?php

namespace App\GraphQL\Types;

use App\Models\Allergy;
use GraphQL\Type\Definition\Type;

class AllergyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "allergies";

	protected $attributes = [
		"name" => "Allergy",
		"description" => "Allergies names",
		"model" => Allergy::class,
	];

	public function columns(): array
	{
		return [
			"name" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The name of the allergy",
			],
		];
	}
}
