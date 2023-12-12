<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\SoftDeleteResolver;
use GraphQL\Type\Definition\Type;

class SoftDeleteArguments extends AdditionalArguments
{
	public static $resolver = SoftDeleteResolver::class;

	public function getArguments()
	{
		return [
			"includeRemoved" => [
				"name" => "includeRemoved",
				"type" => Type::boolean(),
			],
			"onlyRemoved" => [
				"name" => "onlyRemoved",
				"type" => Type::boolean(),
			],
		];
	}
}
