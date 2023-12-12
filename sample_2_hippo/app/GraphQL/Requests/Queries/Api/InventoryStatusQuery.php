<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\InventoryStatus;

class InventoryStatusQuery extends ApiHippoQuery
{
	protected $model = InventoryStatus::class;

	protected $permissionName = "GraphQL: View Inventory Statuses";

	protected $attributes = [
		"name" => "inventoryStatusQuery",
	];
}
