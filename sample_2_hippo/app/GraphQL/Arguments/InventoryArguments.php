<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\InventoryResolver;
use GraphQL\Type\Definition\Type;

class InventoryArguments extends AdditionalArguments
{
	public static $resolver = InventoryResolver::class;

	public function getArguments()
	{
		return [
			"itemId" => [
				"name" => "itemId",
				"type" => Type::int(),
			],
			"statusId" => [
				"name" => "statusId",
				"type" => Type::int(),
			],
			"isOpen" => [
				"name" => "isOpen",
				"type" => Type::boolean(),
			],
			"isRemainingQuantityGreaterThanZero" => [
				"name" => "isRemainingQuantityGreaterThanZero",
				"type" => Type::boolean(),
			],
			"locationId" => [
				"name" => "locationId",
				"type" => Type::int(),
			],
		];
	}
}
