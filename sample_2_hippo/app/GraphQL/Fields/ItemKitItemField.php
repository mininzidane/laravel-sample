<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemKitItemGraphQLType;

class ItemKitItemField extends HippoField
{
	protected $graphQLType = ItemKitItemGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Kit Item";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Kit Items",
	];
}
