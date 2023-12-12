<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\HippoChart;

class HippoChartQuery extends ApiHippoQuery
{
	protected $model = HippoChart::class;

	protected $attributes = [
		"name" => "hippoChartQuery",
	];
}
