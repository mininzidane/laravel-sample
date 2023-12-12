<?php

namespace App\GraphQL\InputObjects\Color;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\ColorGraphQLType;
use App\GraphQL\InputObjects\HippoInputType;

class ColorCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "colorCreateInput",
		"description" => "Input object for creating Color definitions",
	];

	protected $graphQLType = ColorGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the color",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "Species name",
			],
		];
	}
}
