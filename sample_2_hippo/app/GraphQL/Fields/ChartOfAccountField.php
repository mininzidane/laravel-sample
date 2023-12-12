<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ChartOfAccountGraphQLType;

class ChartOfAccountField extends HippoField
{
	protected $graphQLType = ChartOfAccountGraphQLType::class;
	protected $permissionName = "GraphQL: View Chart of Accounts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Chart of Accounts",
	];
}
