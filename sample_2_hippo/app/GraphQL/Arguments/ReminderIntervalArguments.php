<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\ReminderIntervalResolver;
use GraphQL\Type\Definition\Type;

class ReminderIntervalArguments extends AdditionalArguments
{
	public static $resolver = ReminderIntervalResolver::class;

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
