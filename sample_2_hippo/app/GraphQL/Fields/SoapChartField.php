<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\SoapChartGraphQLType;

class SoapChartField extends HippoField
{
	protected $graphQLType = SoapChartGraphQLType::class;
	protected $permissionName = "GraphQL: View Soap Charts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Soap Charts",
	];
}
