<?php

namespace App\GraphQL\InputObjects\MucousMembraneStatus;

use GraphQL\Type\Definition\Type;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\MucousMembraneStatusGraphQLType;

class MucousMembraneStatusCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "mucousMembraneStatusCreateInput",
		"description" =>
			"The input object for creating a mucous membrane status definition",
	];

	protected $graphQLType = MucousMembraneStatusGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The label of the status",
			],
			"abbreviation" => [
				"type" => Type::string(),
				"description" => "The status abbreviation",
			],
		];
	}
}
