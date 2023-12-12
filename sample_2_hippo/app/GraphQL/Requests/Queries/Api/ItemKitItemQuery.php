<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemKitItem;

class ItemKitItemQuery extends ApiHippoQuery
{
	protected $model = ItemKitItem::class;

	protected $permissionName = "GraphQL: View Items";

	protected $attributes = [
		"name" => "itemKitItemQuery",
	];
}
