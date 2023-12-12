<?php

namespace App\GraphQL\Types;

use App\Models\Color;
use GraphQL\Type\Definition\Type;

class ColorGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "color";

	protected $attributes = [
		"name" => "Color",
		"description" => "A patient color",
		"model" => Color::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the color",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "The species of the color",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the color",
			],
			"patientCount" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The color relations to patients",
				"alias" => "patient_count",
			],
		];
	}
}
