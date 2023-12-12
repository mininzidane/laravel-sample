<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\NameResolver;
use GraphQL\Type\Definition\Type;

class NameArguments extends AdditionalArguments
{
	public static $resolver = NameResolver::class;

	public function getArguments()
	{
		return [
			"name" => [
				"name" => "name",
				"type" => Type::string(),
			],
		];
	}
}
