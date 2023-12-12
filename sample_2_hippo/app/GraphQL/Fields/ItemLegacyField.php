<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemLegacyGraphQLType;

class ItemLegacyField extends HippoField
{
	protected $graphQLType = ItemLegacyGraphQLType::class;
	protected $permissionName = "GraphQL: View Legacy Items";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Legacy Items",
	];
}
