<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\StateResolver;
use GraphQL\Type\Definition\Type;

class StateArguments extends AdditionalArguments
{
	public static $resolver = StateResolver::class;

	public function getArguments()
	{
		return [
			"iso" => [
				"name" => "iso",
				"type" => Type::string(),
			],
		];
	}
}
