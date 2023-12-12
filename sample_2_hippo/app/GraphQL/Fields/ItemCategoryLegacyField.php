<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemCategoryLegacyGraphQLType;

class ItemCategoryLegacyField extends HippoField
{
	protected $graphQLType = ItemCategoryLegacyGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Categories";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Categories",
	];
}
