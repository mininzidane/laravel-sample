<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\StateGraphQLType;

class StateField extends HippoField
{
	protected $graphQLType = StateGraphQLType::class;
	protected $permissionName = "GraphQL: View States";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated States",
	];
}
