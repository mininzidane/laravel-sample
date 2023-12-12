<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\ActiveResolver;
use GraphQL\Type\Definition\Type;

class ActiveArguments extends AdditionalArguments
{
	public static $resolver = ActiveResolver::class;

	public function getArguments()
	{
		return [
			"active" => [
				"name" => "active",
				"type" => Type::boolean(),
			],
		];
	}
}
