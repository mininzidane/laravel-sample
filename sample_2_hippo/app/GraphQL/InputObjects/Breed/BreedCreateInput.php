<?php

namespace App\GraphQL\InputObjects\Breed;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\BreedGraphQLType;
use App\GraphQL\InputObjects\HippoInputType;

class BreedCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "breedCreateInput",
		"description" => "The input object for creating a breed",
	];

	protected $graphQLType = BreedGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the breed",
			],

			"species" => [
				"type" => Type::string(),
				"description" => "Species name",
			],
		];
	}
}
