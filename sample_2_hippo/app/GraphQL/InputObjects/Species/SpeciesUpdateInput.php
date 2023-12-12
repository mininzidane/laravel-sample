<?php

namespace App\GraphQL\InputObjects\Species;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\SpeciesGraphQLType;
use App\GraphQL\InputObjects\HippoInputType;

class SpeciesUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "speciesUpdateInput",
		"description" => "The input object for updating a species definition",
	];

	protected $graphQLType = SpeciesGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the species",
			],
		];
	}
}
