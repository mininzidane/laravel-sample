<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\TimestampResolver;
use GraphQL\Type\Definition\Type;

class TimestampArguments extends AdditionalArguments
{
	public static $resolver = TimestampResolver::class;

	public function getArguments()
	{
		return [
			"since" => [
				"name" => "since",
				"type" => Type::string(),
			],
		];
	}
}
