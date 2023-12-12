<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemField;
use App\Models\ItemCategory;
use GraphQL\Type\Definition\Type;

class ItemCategoryGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemCategory";

	protected $attributes = [
		"name" => "ItemCategory",
		"description" => "The available item categories for the subdomain",
		"model" => ItemCategory::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item category",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the item category",
				"rules" => ["required", "unique", "max:255"],
			],
			"relationshipNumber" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The category relations to other tables",
				"alias" => "relationship_number",
			],
			"items" => (new ItemField([
				"isList" => true,
				"description" => "Items associated with this item category",
			]))->toArray(),
			"invoiceItems" => (new InvoiceItemField([
				"isList" => true,
				"description" =>
					"Invoice Items associated with this item category",
			]))->toArray(),
		];
	}
}
