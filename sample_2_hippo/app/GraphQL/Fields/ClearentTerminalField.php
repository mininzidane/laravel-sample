<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ClearentTerminalGraphQLType;

class ClearentTerminalField extends HippoField
{
	protected $graphQLType = ClearentTerminalGraphQLType::class;
	protected $permissionName = "GraphQL: View Clearent Terminals";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Clearent Terminals",
	];
}
