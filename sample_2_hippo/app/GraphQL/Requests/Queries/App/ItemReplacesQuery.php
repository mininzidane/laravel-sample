<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemReplaces;

class ItemReplacesQuery extends AppHippoQuery
{
	protected $model = ItemReplaces::class;

	protected $permissionName = "Item Replaces: Read";

	protected $attributes = [
		"name" => "itemReplacesQuery",
	];
}
