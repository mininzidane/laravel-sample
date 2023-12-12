<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ChartOfAccountField;
use App\GraphQL\Fields\InventoryTransactionField;
use App\GraphQL\Fields\InvoiceAppliedDiscountField;
use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\InvoiceItemTaxField;
use App\GraphQL\Fields\ItemCategoryField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\ItemTypeField;
use App\GraphQL\Fields\ReminderField;
use App\GraphQL\Fields\ReminderIntervalField;
use App\GraphQL\Fields\UserField;
use App\GraphQL\Fields\VaccinationField;
use App\GraphQL\Fields\CreditField;
use App\Models\InvoiceItem;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceItemGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "invoiceItem";

	protected $attributes = [
		"name" => "InvoiceItem",
		"description" => "Details for a given invoice item",
		"model" => InvoiceItem::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"line" => [
				"type" => Type::int(),
				"description" =>
					"The line number of the item in a given invoice",
				"rules" => ["numeric"],
			],
			"quantity" => [
				"type" => Type::float(),
				"description" =>
					"The quantity of the item being sold.  Supports decimals to 5 places.",
				"rules" => ["numeric"],
			],
			"name" => [
				"type" => Type::string(),
				"description" =>
					"The name of the item at the time it was added to the invoice",
			],
			"number" => [
				"type" => Type::string(),
				"description" => "UPC or other identifier for the product",
				"rules" => ["max:191"],
			],
			"price" => [
				"type" => Type::float(),
				"description" =>
					"The price of the item at the time it was added to the invoice",
				"rules" => ["numeric"],
			],
			"discountPercent" => [
				"type" => Type::float(),
				"description" =>
					"The integer percentage that the item is discounted at the time it was added to the invoice",
				"alias" => "discount_percent",
				"rules" => ["numeric", "lte:100"],
			],
			"discountAmount" => [
				"type" => Type::float(),
				"description" =>
					"The flat currency amount the item is to be discounted at the time it was added to the invoice",
				"alias" => "discount_amount",
				"rules" => ["numeric"],
			],
			"total" => [
				"type" => Type::float(),
				"description" =>
					"The total cost of the item at the time it was added to the invoice",
				"rules" => ["numeric"],
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" => "The serial number of the selected item",
				"alias" => "serial_number",
			],
			"administeredDate" => [
				"type" => Type::string(),
				"description" =>
					"The date associated with this item being administered.",
				"alias" => "administered_date",
			],
			"description" => [
				"type" => Type::string(),
				"description" =>
					"The description of the item at the time it was added to the invoice",
			],
			"allowAltDescription" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether to allow alternate descriptions in the invoice",
				"alias" => "allow_alt_description",
			],
			"costPrice" => [
				"type" => Type::float(),
				"description" =>
					"The cost of the item to the clinic when purchased at the time it was added to the invoice",
				"alias" => "cost_price",
				"rules" => ["numeric"],
			],
			"volumePrice" => [
				"type" => Type::float(),
				"description" => "The volume specific pricing change",
				"alias" => "volume_price",
				"rules" => ["numeric"],
			],
			"markupPercentage" => [
				"type" => Type::float(),
				"description" =>
					"The percentage to mark up the cost price by to generate the unit price dynamically at the time it was added to the invoice",
				"alias" => "markup_percentage",
				"rules" => ["numeric"],
			],
			"unitPrice" => [
				"type" => Type::float(),
				"description" =>
					"The configured sale price for the item at the time it was added to the invoice",
				"alias" => "unit_price",
				"rules" => ["numeric"],
			],
			"minimumSaleAmount" => [
				"type" => Type::float(),
				"description" =>
					"The minimum amount to charge for this item regardless of quantity or discounts at the time it was added to the invoice",
				"alias" => "minimum_sale_amount",
				"rules" => ["numeric"],
			],
			"dispensingFee" => [
				"type" => Type::float(),
				"description" =>
					"The fee added to the price of the item for dispensation at the time it was added to the invoice",
				"alias" => "dispensing_fee",
				"rules" => ["numeric"],
			],
			"isVaccine" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was a vaccine at the time it was added to the invoice",
				"alias" => "is_vaccine",
			],
			"isPrescription" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was a prescription at the time it was added to the invoice",
				"alias" => "is_prescription",
			],
			"isSerialized" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was serialized at the time it was added to the invoice",
				"alias" => "is_serialized",
			],
			"isControlledSubstance" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was a controlled substance at the time it was added to the invoice",
				"alias" => "is_controlled_substance",
			],
			"isEuthanasia" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item represented a euthanasia at the time it was added to the invoice",
				"alias" => "is_euthanasia",
			],
			"isReproductive" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item represented a reproductive procedure at the time it was added to the invoice",
				"alias" => "is_reproductive",
			],
			"hideFromRegister" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not to display the item on the available items on the sale register at the time it was added to the invoice",
				"alias" => "hide_from_register",
			],
			"requiresProvider" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item requires a provider to be sold at the time it was added to the invoice",
				"alias" => "requires_provider",
			],
			"isInWellnessPlan" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was in a wellness plan at the time it was added to the invoice",
				"alias" => "is_in_wellness_plan",
			],
			"vcpItemId" => [
				"type" => Type::int(),
				"description" => "The id of an associated VCP item",
				"alias" => "vcp_item_id",
				"rules" => ["numeric"],
			],
			"drugIdentifier" => [
				"type" => Type::string(),
				"description" =>
					"National Drug Id for any controlled substances",
				"alias" => "drug_identifier",
			],
			"belongsToKitId" => [
				"type" => Type::int(),
				"description" => "ID of Invoice Item Kit that added this item.",
				"alias" => "belongs_to_kit_id",
			],
			"isSingleLineKit" => [
				"type" => Type::boolean(),
				"description" =>
					"Display Item Kit as a single line on invoices",
				"alias" => "is_single_line_kit",
			],
			"invoice" => (new InvoiceField([
				"description" =>
					"The invoice associated with this combination of item and invoice",
			]))->toArray(),
			"item" => (new ItemField([
				"description" =>
					"The item associated with this combination of item and invoice",
			]))->toArray(),
			"inventoryTransactions" => (new InventoryTransactionField([
				"isList" => true,
				"description" =>
					"The inventory transaction created by this invoice item",
			]))->toArray(),
			"itemType" => (new ItemTypeField())->toArray(),
			"itemCategory" => (new ItemCategoryField())->toArray(),
			"chartOfAccount" => (new ChartOfAccountField())->toArray(),
			"reminderInterval" => (new ReminderIntervalField())->toArray(),
			"provider" => (new UserField())->toArray(),
			"chart" => [
				"type" => GraphQL::type("chartInterface"),
			],
			"vaccination" => (new VaccinationField())->toArray(),
			"credit" => (new CreditField())->toArray(),
			"invoiceItemTaxes" => (new InvoiceItemTaxField([
				"isList" => true,
			]))->toArray(),
			"reminders" => (new ReminderField(["isList" => true]))->toArray(),
			"appliedDiscounts" => (new InvoiceAppliedDiscountField([
				"isList" => true,
				"description" => "The discounts applied by this item",
			]))->toArray(),
			"discountApplications" => (new InvoiceAppliedDiscountField([
				"isList" => true,
				"description" => "The discounts applied to this item",
			]))->toArray(),
		];
	}
}
