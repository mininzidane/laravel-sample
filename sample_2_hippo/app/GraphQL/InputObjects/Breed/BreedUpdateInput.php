<?php

namespace App\GraphQL\InputObjects\Breed;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\BreedGraphQLType;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;

class BreedUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "breedUpdateInput",
		"description" => "The input object for updating a breed",
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
