<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemTypeGraphQLType;

class ItemTypeField extends HippoField
{
	protected $graphQLType = ItemTypeGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Types";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Types",
	];
}
