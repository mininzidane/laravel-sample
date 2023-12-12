<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\SchedulerSettings;

class SchedulerSettingQuery extends AppHippoQuery
{
	protected $model = SchedulerSettings::class;

	protected $permissionName = "Scheduler Settings: Read";

	protected $attributes = [
		"name" => "SchedulerSettingQuery",
	];
}
