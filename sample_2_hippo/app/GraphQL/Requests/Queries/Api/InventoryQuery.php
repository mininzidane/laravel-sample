<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Inventory;

class InventoryQuery extends ApiHippoQuery
{
	protected $model = Inventory::class;

	protected $permissionName = "GraphQL: View Inventory";

	protected $attributes = [
		"name" => "inventoryQuery",
	];
}
