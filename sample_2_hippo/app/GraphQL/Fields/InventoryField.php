<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InventoryGraphQLType;

class InventoryField extends HippoField
{
	protected $graphQLType = InventoryGraphQLType::class;
	protected $permissionName = "GraphQL: View Inventory";
	protected $isList = false;

	protected $attributes = [
		"description" => "Inventory Records",
	];
}
