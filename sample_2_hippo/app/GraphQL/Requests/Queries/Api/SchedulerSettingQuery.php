<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\SchedulerSettings;

class SchedulerSettingQuery extends ApiHippoQuery
{
	protected $model = SchedulerSettings::class;

	protected $permissionName = "GraphQL: View Scheduler Settings";

	protected $attributes = [
		"name" => "SchedulerSettingQuery",
	];
}
