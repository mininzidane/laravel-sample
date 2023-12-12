<?php

namespace App\GraphQL\Types;

use App\Models\ItemReminderInterval;
use GraphQL\Type\Definition\Type;

class ItemReminderIntervalGraphQLType extends HippoGraphQLType
{
	protected $attributes = [
		"name" => "ItemReminderInterval",
		"description" => "Details of the available reminders for an Item",
		"model" => ItemReminderInterval::class,
	];

	public function fields(): array
	{
		return [
			"item_id" => [
				"type" => Type::int(),
				"description" => "The ID of the item",
			],
			"reminder_interval_id" => [
				"type" => Type::int(),
				"description" => "The ID of the reminder interval",
			],
			"is_default" => [
				"type" => Type::boolean(),
				"description" =>
					"Indicates if this reminder interval is the default for the item",
			],
		];
	}
}
