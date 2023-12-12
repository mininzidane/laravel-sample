<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\EventType;

class EventTypeQuery extends ApiHippoQuery
{
	protected $model = EventType::class;

	protected $permissionName = "GraphQL: View Event Types";

	protected $attributes = [
		"name" => "appointmentTypeQuery",
	];
}
