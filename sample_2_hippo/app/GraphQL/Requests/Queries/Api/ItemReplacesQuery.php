<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemReplaces;

class ItemReplacesQuery extends ApiHippoQuery
{
	protected $model = ItemReplaces::class;

	protected $permissionName = "GraphQL: View Item Replaces";

	protected $attributes = [
		"name" => "itemReplacesQuery",
	];
}
