<?php

namespace App\GraphQL\Requests\Queries;

use App\Models\HippoChart;

abstract class HippoChartQuery extends HippoQuery
{
	protected $model = HippoChart::class;

	protected $attributes = [
		"name" => "hippoChartQuery",
	];
}
