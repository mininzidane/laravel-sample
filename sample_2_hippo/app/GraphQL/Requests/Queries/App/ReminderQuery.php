<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Reminder;

class ReminderQuery extends AppHippoQuery
{
	protected $model = Reminder::class;

	protected $permissionName = "Reminders: Read";

	protected $attributes = [
		"name" => "reminderQuery",
	];
}
