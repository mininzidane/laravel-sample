<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemKitItem;

class ItemKitItemQuery extends AppHippoQuery
{
	protected $model = ItemKitItem::class;

	protected $permissionName = "Item Kit Items: Read";

	protected $attributes = [
		"name" => "itemKitItemQuery",
	];
}
