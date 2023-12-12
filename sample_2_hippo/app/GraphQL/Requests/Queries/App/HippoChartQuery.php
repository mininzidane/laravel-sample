<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\HippoChart;

class HippoChartQuery extends AppHippoQuery
{
	protected $model = HippoChart::class;

	protected $attributes = [
		"name" => "hippoChartQuery",
	];
}
