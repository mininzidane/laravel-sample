<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\HydrationStatus;

class HydrationStatusQuery extends ApiHippoQuery
{
	protected $model = HydrationStatus::class;

	protected $permissionName = "GraphQL: View Hydration Statuses";

	protected $attributes = [
		"name" => "hydrationStatusQuery",
	];
}
