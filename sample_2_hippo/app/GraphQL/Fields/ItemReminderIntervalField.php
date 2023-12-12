<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemReminderIntervalGraphQLType;

class ItemReminderIntervalField extends HippoField
{
	protected $graphQLType = ItemReminderIntervalGraphQLType::class;
	protected $permissionName = "GraphQL: View Reminder Intervals";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Reminder Intervals For An Item",
	];
}
