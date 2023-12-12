<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\HistoryChartGraphQLType;

class HistoryChartField extends HippoField
{
	protected $graphQLType = HistoryChartGraphQLType::class;
	protected $permissionName = "GraphQL: View History Charts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated History Charts",
	];
}
