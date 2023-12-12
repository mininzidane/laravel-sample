<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\AppointmentResolver;
use GraphQL\Type\Definition\Type;

class AppointmentArguments extends AdditionalArguments
{
	public static $resolver = AppointmentResolver::class;

	public function getArguments()
	{
		return [
			"blocked" => [
				"name" => "blocked",
				"type" => Type::boolean(),
			],
		];
	}
}
