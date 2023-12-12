<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemField;
use App\Models\ItemVolumePricing;
use GraphQL\Type\Definition\Type;

class ItemVolumePricingGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemVolumePricing";

	protected $attributes = [
		"name" => "ItemVolumePricing",
		"description" =>
			"Configuration details for discounts at set item quantities purchased",
		"model" => ItemVolumePricing::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::id()),
				"description" => "The id of the item",
			],
			"quantity" => [
				"type" => Type::float(),
				"description" =>
					"The number of items that need to be purchased to activate this pricing",
				"rules" => ["numeric"],
			],
			"unitPrice" => [
				"type" => Type::float(),
				"description" =>
					"The price to be used when the configured quantity amount is reached",
				"rules" => ["numeric"],
				"alias" => "unit_price",
			],
			"item" => (new ItemField([
				"description" =>
					"The item for which this volume pricing applies",
			]))->toArray(),
		];
	}
}
