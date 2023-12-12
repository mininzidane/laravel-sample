<?php

namespace App\GraphQL\InputObjects\Gender;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\GenderGraphQLType;
use App\GraphQL\InputObjects\HippoInputType;

class GenderUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "genderUpdateInput",
		"description" => "The input object for updating a gender definition",
	];

	protected $graphQLType = GenderGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"name" => "name",
				"type" => Type::string(),
				"description" => "The gender name",
			],
			"sex" => [
				"name" => "sex",
				"type" => Type::string(),
				"description" => "The sex abbreviation",
			],
			"neutered" => [
				"name" => "neutered",
				"type" => Type::boolean(),
				"description" => "The neutered flag",
			],
			"species" => [
				"name" => "species",
				"type" => Type::string(),
				"description" => "Species name",
			],
		];
	}
}
