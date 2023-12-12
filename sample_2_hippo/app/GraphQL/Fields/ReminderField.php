<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ReminderGraphQLType;

class ReminderField extends HippoField
{
	protected $graphQLType = ReminderGraphQLType::class;
	protected $permissionName = "GraphQL: View Reminders";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Reminders",
	];
}
