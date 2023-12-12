<?php

namespace App\GraphQL\InputObjects\Gender;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\GenderGraphQLType;
use App\GraphQL\InputObjects\HippoInputType;

class GenderCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "genderCreateInput",
		"description" => "The input object for creating a gender definition",
	];

	protected $graphQLType = GenderGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The gender name",
			],
			"sex" => [
				"type" => Type::string(),
				"description" => "The sex abbreviation",
			],
			"neutered" => [
				"type" => Type::boolean(),
				"description" => "The neutered flag",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "Species name",
			],
		];
	}
}
