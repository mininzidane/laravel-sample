<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PhoneChartGraphQLType;

class PhoneChartField extends HippoField
{
	protected $graphQLType = PhoneChartGraphQLType::class;
	protected $permissionName = "GraphQL: View Phone Charts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Phone Charts",
	];
}
