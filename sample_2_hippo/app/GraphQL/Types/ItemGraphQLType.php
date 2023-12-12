<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ChartOfAccountField;
use App\GraphQL\Fields\InventoryField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemCategoryField;
use App\GraphQL\Fields\ItemKitItemField;
use App\GraphQL\Fields\ItemLocationField;
use App\GraphQL\Fields\ItemReminderReplacesField;
use App\GraphQL\Fields\ItemSpeciesRestrictionField;
use App\GraphQL\Fields\ItemTaxesField;
use App\GraphQL\Fields\ItemTypeField;
use App\GraphQL\Fields\ItemVolumePricingField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\PrescriptionField;
use App\GraphQL\Fields\ReceivingField;
use App\GraphQL\Fields\ReceivingItemField;
use App\GraphQL\Fields\ReminderIntervalField;
use App\GraphQL\Fields\SupplierField;
use App\GraphQL\Fields\TaxField;
use App\GraphQL\Fields\TreatmentSheetTreatmentField;
use App\GraphQL\Fields\VaccinationField;
use App\Models\Item;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ItemGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "item";

	protected $attributes = [
		"name" => "Item",
		"description" => "Details for a given inventory item",
		"model" => Item::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::id()),
				"description" => "The id of the resource",
			],

			//likely need to remove
			"itemType" => (new ItemTypeField([
				"description" => "The type of the item",
			]))->toArray(),

			"itemTypeId" => [
				"type" => Type::int(),
				"description" => "The type of the item",
				"alias" => "type_id",
			],

			"name" => [
				"type" => Type::string(),
				"description" =>
					"The name of the item at the time it was added to the invoice",
			],

			"number" => [
				"type" => Type::string(),
				"description" => "UPC or other identification number",
			],

			"categoryId" => [
				"type" => Type::int(),
				"description" => "The category of the item",
				"alias" => "category_id",
			],

			"chartOfAccountId" => [
				"type" => Type::int(),
				"description" => "The category of the item",
				"alias" => "account_id",
			],

			//likely need to remove
			"itemCategory" => (new ItemCategoryField([
				"description" => "The category of the item",
			]))->toArray(),

			//likely need to remove
			"chartOfAccount" => (new ChartOfAccountField([
				"description" => "Chart of Accounts for this item",
			]))->toArray(),

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

			"applyToRemainder" => [
				"type" => Type::boolean(),
				"description" => "Should this applied to remainder",
				"alias" => "apply_discount_to_remainder",
			],

			"isNonTaxable" => [
				"type" => Type::boolean(),
				"description" => "Should this item be taxed",
				"alias" => "is_non_taxable",
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
			//likely need to remove
			"reminderInterval" => (new ReminderIntervalField())->toArray(),

			"reminderIntervalId" => [
				"type" => Type::int(),
				"description" => "The reminder interval id",
				"alias" => "reminder_interval_id",
			],

			"minimumOnHand" => [
				"type" => Type::float(),
				"description" =>
					"The minimum quantity of this item to keep in stock",
				"alias" => "minimum_on_hand",
			],
			"maximumOnHand" => [
				"type" => Type::float(),
				"description" =>
					"The maximum quantity of this item to keep in stock",
				"alias" => "maximum_on_hand",
			],
			"nextTagNumber" => [
				"type" => Type::int(),
				"description" =>
					"The next tag id to use when an item of this type is sold",
				"alias" => "next_tag_number",
			],
			"drugIdentifier" => [
				"type" => Type::string(),
				"description" =>
					"National Drug Id for any controlled substances",
				"alias" => "drug_identifier",
			],
			"isSingleLineKit" => [
				"type" => Type::boolean(),
				"description" =>
					"Display Item Kit as a single line on invoices",
				"alias" => "is_single_line_kit",
			],
			"oldItemId" => [
				"type" => Type::int(),
				"description" =>
					"The old item id before Hippo 2.0 ~ ospos_items",
				"alias" => "old_item_id",
			],

			"oldItemKitId" => [
				"type" => Type::int(),
				"description" =>
					"The old kit id before Hippo 2.0 ~ ospos_item_kits",
				"alias" => "old_item_kit_id",
			],

			"remaining" => [
				"type" => Type::float(),
				"description" =>
					"The quantity remaining of an item with inventory",
				"selectable" => false,
				"alias" => "remaining",
			],
			"locationQuantity" => [
				"type" => Type::float(),
				"description" =>
					"The quantity remaining of an item with inventory",
				"selectable" => false,
				"args" => [
					"location" => [
						"type" => Type::int(),
						"description" =>
							"The location to calculate the remaining quantity",
					],
				],
				"resolve" => function ($root, $args) {
					$model = $root->getModel();
					if (isset($args["location"])) {
						return $model->getLocationQuantityAttribute(
							$root,
							$args["location"],
						);
					} else {
						return 0;
					}
				},
				"alias" => "locationQuantity",
			],
			"hasInventory" => [
				"type" => Type::boolean(),
				"description" => "Whether or not the item tracks inventory",
				"selectable" => false,
				"alias" => "has_inventory",
			],

			"categoryName" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "Name of the category",
			],

			//likely need to remove
			"taxes" => new TaxField([
				"isList" => true,
				"description" => "Taxes associated with this item",
			]),

			"itemLocations" => (new ItemLocationField([
				"isList" => true,
			]))->toArray(),

			"itemVolumePricing" => (new ItemVolumePricingField([
				"isList" => true,
			]))->toArray(),

			"itemSpeciesRestrictions" => (new ItemSpeciesRestrictionField([
				"isList" => true,
			]))->toArray(),

			"receivings" => (new ReceivingField(["isList" => true]))->toArray(),

			"inventory" => (new InventoryField(["isList" => true]))->toArray(),

			"invoiceItems" => (new InvoiceItemField([
				"isList" => true,
			]))->toArray(),

			//likely need to remove
			"category" => (new ItemCategoryField([
				"isList" => false,
				"description" => "The category of this item",
			]))->toArray(),

			"receivingItems" => (new ReceivingItemField([
				"isList" => true,
				"description" =>
					"The receiving lines associated with this item",
			]))->toArray(),

			"prescriptions" => (new PrescriptionField([
				"isList" => true,
				"description" => "The prescriptions associated with this item",
			]))->toArray(),

			//likely need to remove
			"locations" => (new LocationField([
				"isList" => true,
				"description" => "The locations this item is available",
			]))->toArray(),

			"vaccinations" => (new VaccinationField([
				"isList" => true,
			]))->toArray(),

			"reminderReplaces" => (new ItemReminderReplacesField([
				"isList" => true,
				"description" => "List of reminders this replaces",
			]))->toArray(),

			"itemTaxes" => (new ItemTaxesField([
				"isList" => true,
				"description" => "List of Item Taxes",
			]))->toArray(),

			"itemKitItems" => (new ItemKitItemField([
				"isList" => true,
				"description" => "List of item kits items",
			]))->toArray(),

			"treatmentSheetTreatments" => (new TreatmentSheetTreatmentField([
				"isList" => true,
				"description" => "The associated treatment sheet treatments",
			]))->toArray(),

			"manufacturer" => new SupplierField([
				"isList" => false,
				"description" => "Who makes this thing",
			]),
		];
	}
}
