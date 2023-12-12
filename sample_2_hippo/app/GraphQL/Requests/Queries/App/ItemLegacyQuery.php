<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\ItemArguments;
use App\Models\ItemLegacy;

class ItemLegacyQuery extends AppHippoQuery
{
	protected $model = ItemLegacy::class;

	protected $permissionName = "Legacy Items: Read";

	protected $attributes = [
		"name" => "itemQuery",
	];

	protected $arguments = [ItemArguments::class];
}
