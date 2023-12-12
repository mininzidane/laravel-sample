<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\InventoryArguments;
use App\Models\Inventory;

class InventoryQuery extends AppHippoQuery
{
	protected $model = Inventory::class;

	protected $permissionName = "Inventory: Read";

	protected $attributes = [
		"name" => "inventoryQuery",
	];

	protected $arguments = [InventoryArguments::class];
}
