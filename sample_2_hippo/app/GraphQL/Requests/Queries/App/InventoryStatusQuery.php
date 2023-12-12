<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\InventoryStatus;

class InventoryStatusQuery extends AppHippoQuery
{
	protected $model = InventoryStatus::class;

	protected $permissionName = "Inventory Statuses: Read";

	protected $attributes = [
		"name" => "inventoryStatusQuery",
	];
}
