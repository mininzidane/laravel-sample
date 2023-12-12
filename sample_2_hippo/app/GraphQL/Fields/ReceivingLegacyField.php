<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ReceivingLegacyGraphQLType;

class ReceivingLegacyField extends HippoField
{
	protected $graphQLType = ReceivingLegacyGraphQLType::class;
	protected $permissionName = "GraphQL: View Legacy Receivings";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Legacy Receivings",
	];
}
