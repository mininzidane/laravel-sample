<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InventoryTransactionField;
use App\Models\InventoryTransactionStatus;
use GraphQL\Type\Definition\Type;

class InventoryTransactionStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "inventoryTransactionStatus";

	protected $attributes = [
		"name" => "InventoryTransactionStatus",
		"description" => "Possible statuses for inventory transactions",
		"model" => InventoryTransactionStatus::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" =>
					"The human-readable name of the payment method",
				"rules" => ["max:191"],
			],
			"inventoryTransactions" => (new InventoryTransactionField([
				"isList" => true,
				"description" =>
					"Inventories with this inventory transaction status",
			]))->toArray(),
		];
	}
}
