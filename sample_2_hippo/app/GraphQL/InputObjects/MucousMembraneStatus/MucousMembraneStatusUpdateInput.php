<?php

namespace App\GraphQL\InputObjects\MucousMembraneStatus;

use GraphQL\Type\Definition\Type;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\MucousMembraneStatusGraphQLType;

class MucousMembraneStatusUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "mucousMembraneStatusUpdateInput",
		"description" =>
			"The input object for updating a mucous membrane status definition",
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
