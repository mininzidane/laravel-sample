<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\CreditResolver;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;

class CreditArguments extends AdditionalArguments
{
	public static $resolver = CreditResolver::class;

	public function getArguments()
	{
		return [
			"type" => [
				"name" => "type",
				"type" => Type::string(),
				"rules" => [Rule::in(["Gift Card", "Account Credit"])],
			],
			"owner" => [
				"name" => "owner",
				"type" => Type::int(),
				"rules" => ["numeric"],
			],
			"ownerName" => [
				"name" => "ownerName",
				"type" => Type::string(),
			],
			"number" => [
				"name" => "number",
				"type" => Type::string(),
			],
			"exactMatch" => [
				"name" => "exactMatch",
				"type" => Type::boolean(),
			],
		];
	}
}
