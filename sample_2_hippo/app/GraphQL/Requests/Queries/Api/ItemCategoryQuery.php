<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemCategory;

class ItemCategoryQuery extends ApiHippoQuery
{
	protected $model = ItemCategory::class;

	protected $permissionName = "GraphQL: View Item Categories";

	protected $attributes = [
		"name" => "itemCategoryQuery",
	];
}
