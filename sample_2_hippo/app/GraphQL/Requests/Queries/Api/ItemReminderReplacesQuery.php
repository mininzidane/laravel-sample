<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemReminderReplaces;

class ItemReminderReplacesQuery extends ApiHippoQuery
{
	protected $model = ItemReminderReplaces::class;

	protected $permissionName = "GraphQL: View Item Reminder Replaces";

	protected $attributes = [
		"name" => "itemReminderReplacesQuery",
	];
}
