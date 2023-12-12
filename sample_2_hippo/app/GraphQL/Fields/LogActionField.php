<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\LogActionGraphQLType;

class LogActionField extends HippoField
{
	protected $graphQLType = LogActionGraphQLType::class;
	protected $permissionName = "GraphQL: View Logs";
	protected $isList = false;

	protected $attributes = [
		"description" => "Log Actions",
	];
}
