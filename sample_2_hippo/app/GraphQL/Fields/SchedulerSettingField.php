<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\SchedulerSettingsGraphQLType;

class SchedulerSettingField extends HippoField
{
	protected $graphQLType = SchedulerSettingsGraphQLType::class;
	protected $permissionName = "GraphQL: View Scheduler Settings";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Scheduler Settings",
	];
}
