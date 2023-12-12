<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\ItemArguments;
use App\Models\ItemLegacy;

class ItemLegacyQuery extends ApiHippoQuery
{
	protected $model = ItemLegacy::class;

	protected $permissionName = "GraphQL: View Legacy Items";

	protected $attributes = [
		"name" => "itemQuery",
	];

	protected $arguments = [ItemArguments::class];
}
