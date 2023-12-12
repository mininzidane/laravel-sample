<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InventoryTransactionGraphQLType;

class InventoryTransactionField extends HippoField
{
	protected $graphQLType = InventoryTransactionGraphQLType::class;
	protected $permissionName = "GraphQL: View Inventory Transactions";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Inventory Transaction",
	];
}
