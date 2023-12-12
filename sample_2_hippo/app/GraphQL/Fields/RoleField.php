<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\RoleGraphQLType;

class RoleField extends HippoField
{
	protected $graphQLType = RoleGraphQLType::class;
	protected $permissionName = "GraphQL: View Roles";
	protected $isList = true;

	protected $attributes = [
		"description" => "Associated Roles",
	];
}
