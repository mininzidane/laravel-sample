<?php

namespace App\GraphQL\Types;

use App\Models\HistoryChart;
use GraphQL;

class HistoryChartGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "historyChart";

	protected $attributes = [
		"name" => "HistoryChart",
		"description" => "A History Chart",
		"model" => HistoryChart::class,
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
