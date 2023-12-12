<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemCategoryGraphQLType;

class ItemCategoryField extends HippoField
{
	protected $graphQLType = ItemCategoryGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Categories";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Categories",
	];
}
