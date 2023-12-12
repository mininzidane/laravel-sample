<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemLegacyField;
use App\GraphQL\Fields\OrganizationField;
use App\Models\ReceivingItemLegacy;
use GraphQL\Type\Definition\Type;

class ReceivingItemLegacyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "receivingItemLegacy";

	protected $attributes = [
		"name" => "ReceivingItemLegacy",
		"description" => "Receiving Item",
		"model" => ReceivingItemLegacy::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the receiving item",
			],
			"description" => [
				"type" => Type::string(),
				"description" =>
					"Any remarkable details about the receiving item",
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" =>
					"The serial number for the receiving line if applicable",
			],
			"line" => [
				"type" => Type::nonNull(Type::string()),
				"description" =>
					"The line number for the receiving item within the receiving",
			],
			"quantityPurchased" => [
				"type" => Type::string(),
				"description" =>
					"The number of the associated item purchased in this receiving",
				"alias" => "quantity_purchased",
			],
			"currentQuantity" => [
				"type" => Type::string(),
				"description" =>
					"The quantity of the purchased item remaining in this receiving line",
				"alias" => "current_quantity",
			],
			"lotNumber" => [
				"type" => Type::string(),
				"description" =>
					"The lot number of the receiving line if applicable",
				"alias" => "lot_number",
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" => "The expiration date for the receiving line",
				"alias" => "expiration_date",
			],
			"useForInventory" => [
				"type" => Type::boolean(),
				"description" =>
					" Whether or not the item in this line should be used for inventory",
				"alias" => "use_for_inventory",
			],
			"itemCostPrice" => [
				"type" => Type::string(),
				"description" => "The cost price for the receiving line item",
				"alias" => "item_cost_price",
			],
			"itemUnitPrice" => [
				"type" => Type::string(),
				"description" => "The unit price for the receiving line item",
				"alias" => "item_unit_price",
			],
			"discountPercent" => [
				"type" => Type::string(),
				"description" => "Discount percent for the receiving line",
				"alias" => "discount_percent",
			],
			"item" => (new ItemLegacyField([
				"description" =>
					"The inventory item associated with this receiving line",
			]))->toArray(),
			"organization" => (new OrganizationField([
				"description" =>
					"The organization purchasing the items in the receiving",
			]))->toArray(),
		];
	}
}
