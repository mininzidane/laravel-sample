<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PermissionGraphQLType;

class PermissionField extends HippoField
{
	protected $graphQLType = PermissionGraphQLType::class;
	protected $permissionName = "GraphQL: View Permissions";
	protected $isList = true;

	protected $attributes = [
		"description" => "Associated Permissions",
	];
}
