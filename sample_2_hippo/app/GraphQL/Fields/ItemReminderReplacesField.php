<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemReminderReplacesGraphQLType;

class ItemReminderReplacesField extends HippoField
{
	protected $graphQLType = ItemReminderReplacesGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Reminder Replaces";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Reminder Replaces",
	];
}
