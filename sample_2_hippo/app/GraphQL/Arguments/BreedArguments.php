<?php

namespace App\GraphQL\Arguments;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\BreedResolver;

class BreedArguments extends AdditionalArguments
{
	public static $resolver = BreedResolver::class;

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
