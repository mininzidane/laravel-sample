<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemLocationGraphQLType;

class ItemLocationField extends HippoField
{
	protected $graphQLType = ItemLocationGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Locations";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Locations",
	];
}
