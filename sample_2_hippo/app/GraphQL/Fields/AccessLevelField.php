<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\AccessLevelGraphQLType;

class AccessLevelField extends HippoField
{
	protected $graphQLType = AccessLevelGraphQLType::class;
	protected $permissionName = "GraphQL: View Roles";
	protected $isList = true;

	protected $attributes = [
		"description" => "Associated Roles",
	];
}
