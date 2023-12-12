<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\ItemArguments;
use App\GraphQL\Arguments\NameArguments;
use App\Models\Item;

class ItemQuery extends ApiHippoQuery
{
	protected $model = Item::class;

	protected $permissionName = "GraphQL: View Items";

	protected $attributes = [
		"name" => "itemQuery",
	];

	protected $arguments = [NameArguments::class, ItemArguments::class];
}
