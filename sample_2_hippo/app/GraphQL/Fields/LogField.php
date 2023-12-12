<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\LogGraphQLType;

class LogField extends HippoField
{
	protected $graphQLType = LogGraphQLType::class;
	protected $permissionName = "GraphQL: View Logs";
	protected $isList = false;

	protected $attributes = [
		"description" => "Logs",
	];
}
