<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\EmailResolver;
use GraphQL\Type\Definition\Type;

class EmailArguments extends AdditionalArguments
{
	public static $resolver = EmailResolver::class;

	public function getArguments()
	{
		return [
			"email" => [
				"name" => "email",
				"type" => Type::string(),
			],
		];
	}
}
