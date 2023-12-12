<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemTaxGraphQLType;

class ItemTaxesField extends HippoField
{
	protected $graphQLType = ItemTaxGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Taxes";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Taxes",
	];
}
