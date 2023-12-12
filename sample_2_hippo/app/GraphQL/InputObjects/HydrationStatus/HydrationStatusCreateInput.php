<?php

namespace App\GraphQL\InputObjects\HydrationStatus;

use GraphQL\Type\Definition\Type;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\HydrationStatusGraphQLType;

class HydrationStatusCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "hydrationStatusCreateInput",
		"description" => "The input object for creating a hydration status",
	];

	protected $graphQLType = HydrationStatusGraphQLType::class;

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
