<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\HydrationStatusGraphQLType;

class HydrationStatusField extends HippoField
{
	protected $graphQLType = HydrationStatusGraphQLType::class;
	protected $permissionName = "GraphQL: View Hydration Status";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Hydration Status",
	];
}
