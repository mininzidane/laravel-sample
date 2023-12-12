<?php

namespace App\GraphQL\Types;

use App\Models\ProgressChart;
use GraphQL;

class ProgressChartGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "progressChart";

	protected $attributes = [
		"name" => "ProgressChart",
		"description" => "A Progress Chart",
		"model" => ProgressChart::class,
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
