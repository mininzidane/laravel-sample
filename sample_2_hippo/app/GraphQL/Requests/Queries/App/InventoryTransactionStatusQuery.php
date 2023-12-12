<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\InventoryTransactionStatus;

class InventoryTransactionStatusQuery extends AppHippoQuery
{
	protected $model = InventoryTransactionStatus::class;

	protected $permissionName = "Inventory Transactions: Read";

	protected $attributes = [
		"name" => "inventoryTransactionStatusQuery",
	];
}
