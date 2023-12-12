<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemLegacyField;
use App\Models\ItemCategory;
use GraphQL\Type\Definition\Type;

class ItemCategoryLegacyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemCategoryLegacy";

	protected $attributes = [
		"name" => "ItemCategoryLegacy",
		"description" => "The available item categories for the subdomain",
		"model" => ItemCategory::class,
	];

	public function columns(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the item",
				"alias" => "category",
			],
			"items" => (new ItemLegacyField(["isList" => true]))->toArray(),
		];
	}
}
