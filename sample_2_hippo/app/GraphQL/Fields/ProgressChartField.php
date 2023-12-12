<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ProgressChartGraphQLType;

class ProgressChartField extends HippoField
{
	protected $graphQLType = ProgressChartGraphQLType::class;
	protected $permissionName = "GraphQL: View Progress Charts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Progress Charts",
	];
}
