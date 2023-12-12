<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemType;

class ItemTypeQuery extends ApiHippoQuery
{
	protected $model = ItemType::class;

	protected $permissionName = "GraphQL: View Item Types";

	protected $attributes = [
		"name" => "itemTypeQuery",
	];
}
