<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemField;
use App\Models\ItemTax;
use GraphQL\Type\Definition\Type;

class ItemTaxGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemTax";

	protected $attributes = [
		"name" => "ItemTax",
		"description" => "Taxes for an item",
		"model" => ItemTax::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item",
			],
			"itemId" => [
				"type" => Type::string(),
				"description" => "The descriptive name of the tax",
				"alias" => "item_id",
			],
			"taxId" => [
				"type" => Type::float(),
				"description" =>
					"The percentage of the tax to be applied to register",
				"alias" => "tax_id",
			],
			"items" => (new ItemField([
				"isList" => true,
				"description" => "Items which have this tax",
			]))->toArray(),
		];
	}
}
