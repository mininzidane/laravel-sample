<?php

namespace App\GraphQL\Types;

use App\Models\SoapChart;
use GraphQL;

class SoapChartGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "soapChart";

	protected $attributes = [
		"name" => "SoapChart",
		"description" => "A Soap Chart",
		"model" => SoapChart::class,
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
