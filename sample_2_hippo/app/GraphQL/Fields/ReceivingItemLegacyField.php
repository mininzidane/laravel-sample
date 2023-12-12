<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ReceivingItemLegacyGraphQLType;

class ReceivingItemLegacyField extends HippoField
{
	protected $graphQLType = ReceivingItemLegacyGraphQLType::class;
	protected $permissionName = "GraphQL: View Legacy Receiving Items";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Legacy Receiving Items",
	];
}
