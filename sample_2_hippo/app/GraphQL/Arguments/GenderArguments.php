<?php

namespace App\GraphQL\Arguments;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\GenderResolver;

class GenderArguments extends AdditionalArguments
{
	public static $resolver = GenderResolver::class;

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
