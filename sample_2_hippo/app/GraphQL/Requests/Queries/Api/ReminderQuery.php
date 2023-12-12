<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Reminder;

class ReminderQuery extends ApiHippoQuery
{
	protected $model = Reminder::class;

	protected $permissionName = "GraphQL: View Reminders";

	protected $attributes = [
		"name" => "reminderQuery",
	];
}
