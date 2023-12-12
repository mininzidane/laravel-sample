<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemType;

class ItemTypeQuery extends AppHippoQuery
{
	protected $model = ItemType::class;

	protected $permissionName = "Item Types: Read";

	protected $attributes = [
		"name" => "itemTypeQuery",
	];
}
