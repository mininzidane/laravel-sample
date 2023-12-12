<?php

namespace App\GraphQL\Arguments;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\ColorResolver;

class ColorArguments extends AdditionalArguments
{
	public static $resolver = ColorResolver::class;

	public function getArguments()
	{
		return [
			"species" => [
				"name" => "species",
				"type" => Type::string(),
			],
		];
	}
}
