<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\ItemArguments;
use App\GraphQL\Arguments\NameArguments;
use App\Models\Item;

class ItemQuery extends AppHippoQuery
{
	protected $model = Item::class;

	protected $permissionName = "Items: Read";

	protected $attributes = [
		"name" => "itemQuery",
	];

	protected $arguments = [NameArguments::class, ItemArguments::class];
}
