<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ReminderIntervalGraphQLType;

class ReminderIntervalField extends HippoField
{
	protected $graphQLType = ReminderIntervalGraphQLType::class;
	protected $permissionName = "GraphQL: View Reminder Intervals";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Reminder Intervals",
	];
}
