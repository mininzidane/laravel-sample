<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\DateResolver;
use GraphQL\Type\Definition\Type;

class DatedArguments extends AdditionalArguments
{
	public static $resolver = DateResolver::class;

	public function getArguments()
	{
		return [
			"startDate" => [
				"name" => "startDate",
				"type" => Type::string(),
			],
			"endDate" => [
				"name" => "endDate",
				"type" => Type::string(),
			],
		];
	}
}
