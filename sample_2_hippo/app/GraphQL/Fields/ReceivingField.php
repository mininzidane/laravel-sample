<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ReceivingGraphQLType;

class ReceivingField extends HippoField
{
	protected $graphQLType = ReceivingGraphQLType::class;
	protected $permissionName = "GraphQL: View Receivings";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Receivings",
	];
}
