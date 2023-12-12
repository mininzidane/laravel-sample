<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InventoryTransactionGraphQLType;
use App\GraphQL\Types\InventoryTransactionStatusGraphQLType;

class InventoryTransactionStatusField extends HippoField
{
	protected $graphQLType = InventoryTransactionStatusGraphQLType::class;
	protected $permissionName = "GraphQL: View Inventory Transactions";
	protected $isList = false;

	protected $attributes = [
		"description" => "Inventory Transaction Status",
	];
}
