<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ReceivingItemGraphQLType;

class ReceivingItemField extends HippoField
{
	protected $graphQLType = ReceivingItemGraphQLType::class;
	protected $permissionName = "GraphQL: View Receiving Items";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Receiving Items",
	];
}
