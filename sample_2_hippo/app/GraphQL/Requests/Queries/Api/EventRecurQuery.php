<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\EventRecur;

class EventRecurQuery extends ApiHippoQuery
{
	protected $model = EventRecur::class;

	protected $permissionName = "GraphQL: View Event Recurrences";

	protected $attributes = [
		"name" => "eventRecurTypeQuery",
	];
}
