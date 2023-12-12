<?php

namespace App\GraphQL\Types;

use App\Models\PhoneChart;
use GraphQL;

class PhoneChartGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "phoneChart";

	protected $attributes = [
		"name" => "PhoneChart",
		"description" => "A Phone Chart",
		"model" => PhoneChart::class,
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
