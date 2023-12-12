<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemGraphQLType;

class ItemField extends HippoField
{
	protected $graphQLType = ItemGraphQLType::class;
	protected $permissionName = "GraphQL: View Items";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Items",
	];
}
