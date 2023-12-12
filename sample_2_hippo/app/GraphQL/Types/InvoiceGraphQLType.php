<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InventoryTransactionField;
use App\GraphQL\Fields\InvoiceAppliedDiscountField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\InvoicePaymentField;
use App\GraphQL\Fields\InvoiceStatusField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OwnerField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\ReminderField;
use App\GraphQL\Fields\TreatmentSheetTreatmentField;
use App\GraphQL\Fields\UserField;
use App\Models\Invoice;
use App\Models\Reminder;
use GraphQL\Type\Definition\Type;

class InvoiceGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "invoice";

	protected $attributes = [
		"name" => "Invoice",
		"description" => "Details for a given invoice",
		"model" => Invoice::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"comment" => [
				"type" => Type::string(),
				"description" =>
					"A comment describing any additional information for this invoice",
			],
			"printComment" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether the comment is printed on invoices",
				"alias" => "print_comment",
			],
			"rounding" => [
				"type" => Type::float(),
				"description" =>
					"The currency denomination to round to. Defaults to nearest cent.",
				"rules" => ["numeric"],
			],
			"isTaxable" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether taxes are applied to this invoice",
				"alias" => "is_taxable",
			],
			"isActive" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether invoice is active",
				"alias" => "active",
			],
			"total" => [
				"type" => Type::float(),
				"description" => "The final total for the invoice",
				"rules" => ["numeric"],
			],
			"totalPayments" => [
				"type" => Type::float(),
				"description" =>
					"The total of all applied payments for this invoice",
				"rules" => ["numeric"],
			],
			"amountDue" => [
				"type" => Type::float(),
				"description" => "The amount left due on the invoice",
				"rules" => ["numeric"],
			],
			"active" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the invoice is the most recently active invoice for a given patient",
			],
			"isBulk" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the invoice is the most recently active invoice for a given patient",
				"selectable" => false,
			],
			"bulkPaymentId" => [
				"type" => Type::int(),
				"description" =>
					"Whether or not the invoice is the most recently active invoice for a given patient",
				"selectable" => false,
			],
			"completedAt" => [
				"type" => Type::string(),
				"description" => "When the invoice is paid in full",
				"alias" => "completed_at",
				"rules" => ["date"],
			],
			"invoiceStatus" => (new InvoiceStatusField([
				"description" => "The invoice status for this invoice",
			]))->toArray(),
			"location" => (new LocationField([
				"description" =>
					"Which practice location this invoice is associated with",
			]))->toArray(),
			"patient" => (new PatientField([
				"description" =>
					"Which patient this invoice is associated with",
			]))->toArray(),
			"owner" => (new OwnerField([
				"description" => "Which owner is associated with this invoice",
			]))->toArray(),
			"user" => (new UserField([
				"description" =>
					"Which practice user is responsible for this invoice",
			]))->toArray(),
			"invoiceItems" => (new InvoiceItemField([
				"isList" => true,
				"description" => "The items included in this invoice",
			]))->toArray(),
			"invoicePayments" => (new InvoicePaymentField([
				"isList" => true,
				"description" =>
					'Payments made towards this invoice\'s balance',
			]))->toArray(),
			"inventoryTransactions" => (new InventoryTransactionField([
				"isList" => true,
				"description" =>
					"Inventory transaction associated with this invoice",
			]))->toArray(),
			"treatmentSheetTreatments" => (new TreatmentSheetTreatmentField([
				"isList" => true,
				"description" => "The associated treatment sheet treatments",
			]))->toArray(),
			"appliedDiscounts" => (new InvoiceAppliedDiscountField([
				"isList" => true,
				"description" =>
					"The associated applied discounts on this invoice",
			]))->toArray(),
			"reminders" => (new ReminderField([
				"isList" => true,
				"description" => "Reminders created by this invoice",
			]))->toArray(),
		];
	}
}
