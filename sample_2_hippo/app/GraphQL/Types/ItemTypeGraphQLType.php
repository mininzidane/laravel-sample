<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemField;
use App\Models\ItemType;
use GraphQL\Type\Definition\Type;

class ItemTypeGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemType";

	protected $attributes = [
		"name" => "ItemType",
		"description" =>
			"A type of item available for configuration on an item",
		"model" => ItemType::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The descriptive name of the item type",
				"rules" => ["max:255"],
			],
			"items" => (new ItemField([
				"isList" => true,
				"description" => "Items associated with this item type",
			]))->toArray(),
			"invoiceItems" => (new InvoiceItemField([
				"isList" => true,
				"description" => "Invoice Items associated with this item type",
			]))->toArray(),
		];
	}
}
