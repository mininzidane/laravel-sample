<?php

namespace App\GraphQL\Types;

use App\Models\EmailChart;
use GraphQL;

class EmailChartGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "emailChart";

	protected $attributes = [
		"name" => "EmailChart",
		"description" => "An Email Chart",
		"model" => EmailChart::class,
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
