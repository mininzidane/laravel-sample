<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemReminderReplaces;

class ItemReminderReplacesQuery extends AppHippoQuery
{
	protected $model = ItemReminderReplaces::class;

	protected $permissionName = "Legacy Item Reminder Replaces: Read";

	protected $attributes = [
		"name" => "itemReminderReplacesQuery",
	];
}
