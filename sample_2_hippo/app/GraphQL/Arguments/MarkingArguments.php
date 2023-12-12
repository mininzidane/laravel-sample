<?php

namespace App\GraphQL\Arguments;

use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\MarkingResolver;

class MarkingArguments extends AdditionalArguments
{
	public static $resolver = MarkingResolver::class;

	public function getArguments()
	{
		return [
			"species" => [
				"name" => "species",
				"type" => Type::string(),
			],
		];
	}
}
