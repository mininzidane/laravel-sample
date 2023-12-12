<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\InventoryTransactionArguments;
use App\Models\InventoryTransaction;

class InventoryTransactionQuery extends AppHippoQuery
{
	protected $model = InventoryTransaction::class;

	protected $permissionName = "Inventory Transactions: Read";

	protected $attributes = [
		"name" => "inventoryTransactionQuery",
	];

	protected $arguments = [InventoryTransactionArguments::class];
}
