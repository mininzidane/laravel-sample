<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\InventoryTransaction;

class InventoryTransactionQuery extends ApiHippoQuery
{
	protected $model = InventoryTransaction::class;

	protected $permissionName = "GraphQL: View Inventory Transactions";

	protected $attributes = [
		"name" => "inventoryTransactionQuery",
	];
}
