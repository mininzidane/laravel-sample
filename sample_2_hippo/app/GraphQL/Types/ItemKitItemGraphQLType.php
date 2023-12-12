<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemField;
use App\Models\ItemKitItem;
use GraphQL\Type\Definition\Type;

class ItemKitItemGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemKitItem";

	protected $attributes = [
		"name" => "ItemKitItem",
		"description" =>
			"Details for an item and its details when included in an associated kit",
		"model" => ItemKitItem::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::id()),
				"description" => "The id of the resource",
			],
			"item_kit_id" => [
				"type" => Type::id(),
				"description" =>
					"The number of the item included in the item kit",
				"alias" => "item_kit_id",
				"rules" => ["numeric"],
			],
			"quantity" => [
				"type" => Type::float(),
				"description" =>
					"The number of the item included in the item kit",
				"alias" => "quantity",
				"rules" => ["numeric"],
			],
			"item" => (new ItemField([
				"description" =>
					"One of the items associated with the also associated kit",
			]))->toArray(),
			"itemKit" => (new ItemField([
				"description" => "The item kit that items are included in",
			]))->toArray(),
		];
	}
}
