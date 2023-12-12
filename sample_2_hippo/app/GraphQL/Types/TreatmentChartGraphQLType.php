<?php

namespace App\GraphQL\Types;

use App\Models\TreatmentChart;
use GraphQL;

class TreatmentChartGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "treatmentChart";

	protected $attributes = [
		"name" => "TreatmentChart",
		"description" => "A Treatment Chart",
		"model" => TreatmentChart::class,
	];

	public function interfaces(): array
	{
		return [GraphQL::type("chartInterface")];
	}

	public function columns(): array
	{
		$interface = GraphQL::type("chartInterface");

		return $interface->getFields();
	}
}
