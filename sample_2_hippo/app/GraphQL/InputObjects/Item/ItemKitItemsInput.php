<?php

namespace App\GraphQL\InputObjects\Item;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ItemKitItemGraphQLType;
use GraphQL\Type\Definition\Type;

class ItemKitItemsInput extends HippoInputType
{
	protected $attributes = [
		"name" => "itemKitItemsInput",
		"description" => "Kit Items in Items",
	];

	protected $requiredFields = ["item_id", "quantity"];
	protected $graphQLType = ItemKitItemGraphQLType::class;
	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::id(),
				"description" => "id of the item kit item",
			],
			"item_id" => [
				"name" => "item_id",
				"type" => Type::int(),
				"description" => "Id of the item",
			],
			"quantity" => [
				"name" => "quantity",
				"type" => Type::float(),
				"description" => "Quantity of kit items in kit",
			],
			"item_name" => [
				"name" => "item_name",
				"type" => Type::string(),
				"description" => "Name of item",
			],
		];
	}
}
