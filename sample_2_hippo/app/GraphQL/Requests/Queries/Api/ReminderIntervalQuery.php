<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\ReminderIntervalArguments;
use App\Models\ReminderInterval;

class ReminderIntervalQuery extends ApiHippoQuery
{
	protected $model = ReminderInterval::class;

	protected $permissionName = "GraphQL: View Reminder Intervals";

	protected $attributes = [
		"name" => "reminderIntervalQuery",
	];

	protected $arguments = [ReminderIntervalArguments::class];
}
