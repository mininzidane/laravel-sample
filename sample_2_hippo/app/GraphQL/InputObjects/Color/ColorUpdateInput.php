<?php

namespace App\GraphQL\InputObjects\Color;

use App\GraphQL\InputObjects\HippoInputType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\ColorGraphQLType;

class ColorUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "colorUpdateInput",
		"description" => "Input object for updating Color definitions",
	];

	protected $graphQLType = ColorGraphQLType::class;

	protected $inputObject = true;

	//protected $requiredFields = ["name", "species"];

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
