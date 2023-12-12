<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemLocation;

class ItemLocationQuery extends ApiHippoQuery
{
	protected $model = ItemLocation::class;

	protected $permissionName = "GraphQL: View Item Locations";

	protected $attributes = [
		"name" => "itemLocationQuery",
	];
}
