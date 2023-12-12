<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\EmailChartGraphQLType;

class EmailChartField extends HippoField
{
	protected $graphQLType = EmailChartGraphQLType::class;
	protected $permissionName = "GraphQL: View Email Charts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Email Charts",
	];
}
