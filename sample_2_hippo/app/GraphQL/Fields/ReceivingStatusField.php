<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ReceivingStatusGraphQLType;

class ReceivingStatusField extends HippoField
{
	protected $graphQLType = ReceivingStatusGraphQLType::class;
	protected $permissionName = "GraphQL: View Receiving Statuses";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Receiving Status",
	];
}
