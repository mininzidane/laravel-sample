<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\InventoryTransactionResolver;
use GraphQL\Type\Definition\Type;

class InventoryTransactionArguments extends AdditionalArguments
{
	public static $resolver = InventoryTransactionResolver::class;

	public function getArguments()
	{
		return [
			"itemId" => [
				"name" => "itemId",
				"type" => Type::int(),
			],
			"userId" => [
				"name" => "userId",
				"type" => Type::int(),
			],
			"statusId" => [
				"name" => "statusId",
				"type" => Type::int(),
			],
			"locationId" => [
				"name" => "locationId",
				"type" => Type::int(),
			],
		];
	}
}
