<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemVolumePricingGraphQLType;

class ItemVolumePricingField extends HippoField
{
	protected $graphQLType = ItemVolumePricingGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Volume Pricing";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Volume Pricing Information",
	];
}
