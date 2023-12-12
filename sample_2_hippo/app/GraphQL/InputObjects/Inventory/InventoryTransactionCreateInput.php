<?php

namespace App\GraphQL\InputObjects\Inventory;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InventoryTransactionGraphQLType;
use GraphQL\Type\Definition\Type;

class InventoryTransactionCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "inventoryTransactionCreateInput",
		"description" =>
			"The input object for creating a new inventory transaction",
	];

	protected $graphQLType = InventoryTransactionGraphQLType::class;

	public function fields(): array
	{
		return [
			"inventoryId" => [
				"type" => Type::int(),
				"description" => "ID of the item for the transaction",
				"alias" => "inventory_id",
			],
			"quantity" => [
				"type" => Type::float(),
				"description" => "The quantity of this receiving item",
				"default" => 0,
				"rules" => ["required"],
			],
			"statusId" => [
				"type" => Type::int(),
				"description" => "ID of the status from",
				"alias" => "status_id",
			],
			"comment" => [
				"type" => Type::string(),
				"description" => "Any additional details to be recorded",
			],
			"transactionAt" => [
				"type" => Type::string(),
				"description" => "When the transaction took place",
				"alias" => "transaction_at",
			],
			"isShrink" => [
				"type" => Type::boolean(),
				"description" => "Whether the inventory was reduced",
				"alias" => "is_shrink",
			],
			"shrinkReason" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "shrink_reason",
			],
		];
	}
}
