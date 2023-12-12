<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\ClearentTokenResolver;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;

class ClearentTokenArguments extends AdditionalArguments
{
	public static $resolver = ClearentTokenResolver::class;

	public function getArguments()
	{
		return [
			"owner" => [
				"name" => "owner",
				"type" => Type::int(),
				"rules" => ["numeric"],
			],
		];
	}
}
