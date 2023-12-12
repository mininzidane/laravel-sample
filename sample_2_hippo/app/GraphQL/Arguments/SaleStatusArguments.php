<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\SaleStatusResolver;
use GraphQL\Type\Definition\Type;

class SaleStatusArguments extends AdditionalArguments
{
	public static $resolver = SaleStatusResolver::class;

	public function getArguments()
	{
		return [
			"saleStatus" => [
				"name" => "saleStatus",
				"type" => Type::string(),
			],
		];
	}
}
