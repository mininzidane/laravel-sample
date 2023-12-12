<?php

namespace App\GraphQL\InputObjects\Markings;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\MarkingsGraphQLType;
use App\GraphQL\InputObjects\HippoInputType;

class MarkingsUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "markingsUpdateInput",
		"description" => "The input object for updating a markings definition",
	];

	protected $graphQLType = MarkingsGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the marking",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "Species name",
			],
		];
	}
}
