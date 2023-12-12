<?php

namespace App\GraphQL\InputObjects\Item;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ItemKitItemGraphQLType;
use App\GraphQL\Types\ItemReminderReplacesGraphQLType;
use GraphQL\Type\Definition\Type;

class ItemReminderReplacesInput extends HippoInputType
{
	protected $attributes = [
		"name" => "itemReminderReplacesInput",
		"description" => "Reminder replaces for an item",
	];

	protected $graphQLType = ItemReminderReplacesGraphQLType::class;
	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::id(),
				"description" => "id of the replacement",
			],
		];
	}
}
