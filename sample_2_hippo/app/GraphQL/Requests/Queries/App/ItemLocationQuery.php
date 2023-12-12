<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemLocation;

class ItemLocationQuery extends AppHippoQuery
{
	protected $model = ItemLocation::class;

	protected $permissionName = "Item Locations: Read";

	protected $attributes = [
		"name" => "itemLocationQuery",
	];
}
