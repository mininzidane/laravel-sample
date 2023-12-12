<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InventoryTransactionField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\ReceivingItemField;
use App\Models\Inventory;
use GraphQL\Type\Definition\Type;

class InventoryGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "inventory";

	protected $attributes = [
		"name" => "Inventory",
		"description" => "An entry in the inventory tracking table",
		"model" => Inventory::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"itemId" => [
				"type" => Type::int(),
				"description" => "The id of the Items resource",
				"alias" => "item_id",
			],
			"receivingItemId" => [
				"type" => Type::int(),
				"description" => "The id of the receiving item",
				"alias" => "receiving_item_id",
			],
			"locationId" => [
				"type" => Type::int(),
				"description" => "The id of the location",
				"alias" => "location_id",
			],
			"statusId" => [
				"type" => Type::int(),
				"description" => "The status of the item",
				"alias" => "status_id",
			],
			"lotNumber" => [
				"type" => Type::string(),
				"description" =>
					"The lot number associated with this inventory change",
				"alias" => "lot_number",
			],
			"receivedAt" => [
				"type" => Type::string(),
				"description" => "The date the inventory was received",
				"alias" => "receivedAt",
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" =>
					"The associated serial number of the item if one is available",
				"alias" => "serial_number",
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" => "The date when the associated item expires",
				"alias" => "expiration_date",
			],
			"startingQuantity" => [
				"type" => Type::float(),
				"description" => "The quantity on hand when received",
				"rules" => ["numeric"],
				"alias" => "starting_quantity",
			],
			"remainingQuantity" => [
				"type" => Type::float(),
				"description" => "The quantity on hand currently",
				"rules" => ["numeric"],
				"alias" => "remaining_quantity",
			],
			"isOpen" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag indicating whether an associated contain is open or not",
				"alias" => "is_open",
			],
			"openedAt" => [
				"type" => Type::string(),
				"description" =>
					"If the item was opened, this is the associated opening date",
				"alias" => "opened_at",
			],
			"remaining" => [
				"type" => Type::int(),
				"description" =>
					"The quantity remaining of an item with inventory",
				"selectable" => false,
				"alias" => "remaining",
			],
			"name" => [
				"type" => Type::string(),
				"description" =>
					"The name of the item at the time it was added to the invoice",
			],
			"item" => (new ItemField([
				"description" => "The item being kept track of",
			]))->toArray(),
			"receivingItem" => (new ReceivingItemField([
				"description" => "Which receiving item is monitored",
			]))->toArray(),
			"inventoryTransactions" => (new InventoryTransactionField([
				"isList" => true,
				"description" =>
					"The inventory transactions associated with this inventory",
			]))->toArray(),
		];
	}
}
