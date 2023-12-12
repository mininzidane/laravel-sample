<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\LogActionResolver;
use GraphQL\Type\Definition\Type;

class LogActionArguments extends AdditionalArguments
{
	public static $resolver = LogActionResolver::class;

	public function getArguments()
	{
		return [
			"action" => [
				"name" => "action",
				"type" => Type::string(),
			],
		];
	}
}
