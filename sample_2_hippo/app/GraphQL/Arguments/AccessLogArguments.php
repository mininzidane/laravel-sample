<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\AccessLogResolver;
use GraphQL\Type\Definition\Type;

class AccessLogArguments extends AdditionalArguments
{
	public static $resolver = AccessLogResolver::class;

	public function getArguments()
	{
		return [
			"actionId" => [
				"name" => "actionId",
				"type" => Type::string(),
			],
			"userId" => [
				"name" => "userId",
				"type" => Type::string(),
			],
		];
	}
}
