<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\ReminderIntervalArguments;
use App\Models\ReminderInterval;

class ReminderIntervalQuery extends AppHippoQuery
{
	protected $model = ReminderInterval::class;

	protected $permissionName = "Reminder Intervals: Read";

	protected $attributes = [
		"name" => "reminderIntervalQuery",
	];

	protected $arguments = [ReminderIntervalArguments::class];
}
