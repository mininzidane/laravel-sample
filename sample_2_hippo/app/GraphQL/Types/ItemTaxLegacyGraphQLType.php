<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemLegacyField;
use App\Models\ItemTaxLegacy;
use GraphQL\Type\Definition\Type;

class ItemTaxLegacyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemTaxLegacy";

	protected $attributes = [
		"name" => "ItemTaxLegacy",
		"description" => "Taxes for an item",
		"model" => ItemTaxLegacy::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the item",
			],
			"percent" => [
				"type" => Type::string(),
				"description" => "",
			],
			"item" => (new ItemLegacyField())->toArray(),
		];
	}
}
