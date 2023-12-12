<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Event;

class EventQuery extends ApiHippoQuery
{
	protected $model = Event::class;

	protected $permissionName = "GraphQL: View Events";

	protected $attributes = [
		"name" => "eventQuery",
	];
}
