<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemField;
use App\Models\ItemReminderReplaces;
use GraphQL\Type\Definition\Type;

class ItemReminderReplacesGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemReminderReplaces";

	protected $attributes = [
		"name" => "ItemReminderReplaces",
		"description" => "The reminders that replace this item",
		"model" => ItemReminderReplaces::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item category",
			],
			"replacedItem" => (new ItemField())->toArray(),
			"newItem" => (new ItemField())->toArray(),
		];
	}
}
