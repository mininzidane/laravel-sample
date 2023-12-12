<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\EventRecur;

class EventRecurQuery extends AppHippoQuery
{
	protected $model = EventRecur::class;

	protected $permissionName = "Event Recurrences: Read";

	protected $attributes = [
		"name" => "eventRecurTypeQuery",
	];
}
