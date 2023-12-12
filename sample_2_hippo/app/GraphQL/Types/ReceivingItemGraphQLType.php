<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InventoryField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\ReceivingField;
use App\Models\ReceivingItem;
use GraphQL\Type\Definition\Type;

class ReceivingItemGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "receivingItem";

	protected $attributes = [
		"name" => "ReceivingItem",
		"description" => "Receiving Item",
		"model" => ReceivingItem::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the receiving item",
			],
			"line" => [
				"type" => Type::int(),
				"description" =>
					"The line number for a specific item within a receiving",
				"rules" => ["numeric", "gte:0"],
			],
			"quantity" => [
				"type" => Type::float(),
				"description" => "The number of the associated item received",
				"rules" => ["numeric", "gte:0"],
			],
			"comment" => [
				"type" => Type::string(),
				"description" =>
					"Any noteworthy details about the receiving item",
			],
			"costPrice" => [
				"type" => Type::float(),
				"description" =>
					"The price of the items when they were purchased",
				"rules" => ["numeric", "gte:0"],
				"alias" => "cost_price",
			],
			"discountPercentage" => [
				"type" => Type::float(),
				"description" => "Any discount applied to the item",
				"rules" => ["numeric"],
				"alias" => "discount_percentage",
			],
			"unitPrice" => [
				"type" => Type::float(),
				"description" => "",
				"rules" => ["numeric", "gte:0"],
				"alias" => "unit_price",
			],
			"receiving" => (new ReceivingField([
				"description" =>
					"The receiving this item was received as part of",
			]))->toArray(),
			"item" => (new ItemField([
				"description" => "Which item was received",
			]))->toArray(),
			"inventory" => (new InventoryField([
				"isList" => true,
				"description" =>
					"The Inventory records associated with this combination of receiving and item",
			]))->toArray(),
			"invoiceItems" => (new InvoiceItemField([
				"isList" => true,
				"description" =>
					"The invoice items associated with this receiving item",
			]))->toArray(),
		];
	}
}
