<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemTaxLegacyGraphQLType;

class ItemTaxLegacyField extends HippoField
{
	protected $graphQLType = ItemTaxLegacyGraphQLType::class;
	protected $permissionName = "GraphQL: View Legacy Item Taxes";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Legacy Item Taxes",
	];
}
