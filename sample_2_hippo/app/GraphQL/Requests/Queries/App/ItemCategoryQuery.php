<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemCategory;

class ItemCategoryQuery extends AppHippoQuery
{
	protected $model = ItemCategory::class;

	protected $permissionName = "Item Categories: Read";

	protected $attributes = [
		"name" => "itemCategoryQuery",
	];
}
