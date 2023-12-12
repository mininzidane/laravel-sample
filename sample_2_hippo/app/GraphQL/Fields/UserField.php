<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\UserGraphQLType;
use GraphQL\Type\Definition\Type;

class UserField extends HippoField
{
	protected $graphQLType = UserGraphQLType::class;
	protected $permissionName = "GraphQL: View Users";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Users",
	];
}
