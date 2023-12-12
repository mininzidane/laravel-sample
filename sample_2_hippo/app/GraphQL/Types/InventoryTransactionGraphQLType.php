<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InventoryField;
use App\GraphQL\Fields\InventoryTransactionStatusField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\UserField;
use App\Models\InventoryTransaction;
use GraphQL\Type\Definition\Type;

class InventoryTransactionGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "inventoryTransaction";

	protected $attributes = [
		"name" => "InventoryTransaction",
		"description" => "An entry in the inventory transaction",
		"model" => InventoryTransaction::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"inventoryId" => [
				"type" => Type::int(),
				"description" => "ID of the item for the transaction",
				"alias" => "inventory_id",
			],
			"userId" => [
				"type" => Type::int(),
				"description" => "ID of the user creating the transaction",
				"alias" => "user_id",
			],
			"invoiceItemId" => [
				"type" => Type::int(),
				"description" => "ID of the inventory item for the transaction",
				"alias" => "invoice_item_id",
			],
			"statusId" => [
				"type" => Type::int(),
				"description" => "Status of the inventory item",
				"alias" => "status_id",
			],
			"quantity" => [
				"type" => Type::float(),
				"description" =>
					"The quantity to add or remove from the inventory",
				"rules" => ["numeric"],
			],
			"comment" => [
				"type" => Type::string(),
				"description" => "Any additional details to be recorded",
			],
			"transactionAt" => [
				"type" => Type::string(),
				"description" => "When the transaction took place",
				"alias" => "transaction_at",
			],
			"isShrink" => [
				"type" => Type::boolean(),
				"description" => "Whether the inventory was reduced",
				"alias" => "is_shrink",
			],
			"shrinkReason" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "shrink_reason",
			],
			"openedAt" => [
				"type" => Type::string(),
				"description" => "Date and time the transaction was opened",
				"alias" => "opened_at",
			],
			"inventory" => (new InventoryField([
				"description" =>
					"The inventory record associated with this transaction",
			]))->toArray(),
			"invoiceItem" => (new InvoiceItemField([
				"description" =>
					"The invoice item that generated this transaction",
			]))->toArray(),
			"user" => (new UserField([
				"description" =>
					"The user that created this inventory transaction",
			]))->toArray(),
			"inventoryTransactionStatus" => (new InventoryTransactionStatusField(
				["description" => "The status of the inventory transaction"],
			))->toArray(),
			"unitPriceAtTime" => [
				"type" => Type::float(),
				"description" => "Calculated unit price based on action log",
				"selectable" => false,
				"resolve" => function (InventoryTransaction $root) {
					return $root->getUnitPriceData()["unitPrice"];
				},
			],
			"unitPriceChangedBy" => [
				"type" => Type::string(),
				"description" => "User full name who changed the unit price",
				"selectable" => false,
				"resolve" => function (InventoryTransaction $root) {
					return $root->getUnitPriceData()["changedBy"];
				},
			],
			"unitPriceAdditionalComment" => [
				"type" => Type::string(),
				"description" =>
					"Comment text with affected record id by unit price",
				"selectable" => false,
				"resolve" => function (InventoryTransaction $root) {
					return $root->getUnitPriceData()["additionalComment"];
				},
			],
		];
	}
}
