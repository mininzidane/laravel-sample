<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Event;

class EventQuery extends AppHippoQuery
{
	protected $model = Event::class;

	protected $permissionName = "Events: Read";

	protected $attributes = [
		"name" => "eventQuery",
	];
}
