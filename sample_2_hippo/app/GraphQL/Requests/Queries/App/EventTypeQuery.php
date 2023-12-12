<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\EventType;

class EventTypeQuery extends AppHippoQuery
{
	protected $model = EventType::class;

	protected $permissionName = "Event Types: Read";

	protected $attributes = [
		"name" => "appointmentTypeQuery",
	];
}
