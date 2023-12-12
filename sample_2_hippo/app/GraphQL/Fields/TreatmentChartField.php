<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\TreatmentChartGraphQLType;

class TreatmentChartField extends HippoField
{
	protected $graphQLType = TreatmentChartGraphQLType::class;
	protected $permissionName = "GraphQL: View Treatment Charts";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Treatment Charts",
	];
}
