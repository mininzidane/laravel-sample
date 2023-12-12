<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\PhoneResolver;
use GraphQL\Type\Definition\Type;

class PhoneArguments extends AdditionalArguments
{
	public static $resolver = PhoneResolver::class;

	public function getArguments()
	{
		return [
			"phone" => [
				"name" => "phone",
				"type" => Type::string(),
			],
		];
	}
}
