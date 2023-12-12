<?php

namespace App\GraphQL\InputObjects\Item;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ItemGraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ItemCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "ItemCreateInput",
		"description" => "An item to be updated or created",
	];

	protected $graphQLType = ItemGraphQLType::class;

	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" =>
					"The name of the item at the time it was added to the invoice",
				"rules" => [],
			],
			"number" => [
				"type" => Type::string(),
				"description" => "UPC or other identification number",
				"rules" => [],
			],
			"itemTypeId" => [
				"type" => Type::int(),
				"description" => "Item type id",
				"relation" => true,
				"default" => null,
				"alias" => "type_id",
			],
			"chartOfAccountId" => [
				"type" => Type::int(),
				"description" => "The category of the item",
				"alias" => "account_id",
				"rules" => [],
			],
			"categoryId" => [
				"type" => Type::int(),
				"description" => "The category of the item",
				"alias" => "category_id",
				"rules" => [],
			],
			"description" => [
				"type" => Type::string(),
				"description" =>
					"The description of the item at the time it was added to the invoice",
				"rules" => [],
			],
			"allowAltDescription" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether to allow alternate descriptions in the invoice",
				"alias" => "allow_alt_description",
				"rules" => [],
			],

			"itemCategory" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The category of the item",
				"rules" => [],
			],
			"chartOfAccount" => [
				"type" => Type::int(),
				"description" => "Account Id",
				"relation" => true,
				"default" => null,
				"alias" => "account_id",
				"rules" => [],
			],
			"isVaccine" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was a vaccine at the time it was added to the invoice",
				"alias" => "is_vaccine",
				"rules" => [],
			],
			"isPrescription" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was a prescription at the time it was added to the invoice",
				"alias" => "is_prescription",
				"rules" => [],
			],
			"isSerialized" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was serialized at the time it was added to the invoice",
				"alias" => "is_serialized",
				"rules" => [],
			],
			"isControlledSubstance" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was a controlled substance at the time it was added to the invoice",
				"alias" => "is_controlled_substance",
				"rules" => [],
			],
			"isInWellnessPlan" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item was in a wellness plan at the time it was added to the invoice",
				"alias" => "is_in_wellness_plan",
				"rules" => [],
			],
			"isEuthanasia" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item represented a euthanasia at the time it was added to the invoice",
				"alias" => "is_euthanasia",
				"rules" => [],
			],
			"isReproductive" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item represented a reproductive procedure at the time it was added to the invoice",
				"alias" => "is_reproductive",
				"rules" => [],
			],
			"requiresProvider" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not the item requires a provider to be sold at the time it was added to the invoice",
				"alias" => "requires_provider",
				"rules" => [],
			],
			"hideFromRegister" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not to display the item on the available items on the sale register at the time it was added to the invoice",
				"alias" => "hide_from_register",
				"rules" => [],
			],
			"costPrice" => [
				"type" => Type::float(),
				"description" =>
					"The cost of the item to the clinic when purchased at the time it was added to the invoice",
				"alias" => "cost_price",
				"rules" => ["numeric"],
			],
			"minimumSaleAmount" => [
				"type" => Type::float(),
				"description" =>
					"The minimum amount to charge for this item regardless of quantity or discounts at the time it was added to the invoice",
				"alias" => "minimum_sale_amount",
				"rules" => ["numeric"],
			],
			"markupPercentage" => [
				"type" => Type::float(),
				"description" =>
					"The percentage to mark up the cost price by to generate the unit price dynamically at the time it was added to the invoice",
				"alias" => "markup_percentage",
				"rules" => ["numeric"],
			],
			"dispensingFee" => [
				"type" => Type::float(),
				"description" =>
					"The fee added to the price of the item for dispensation at the time it was added to the invoice",
				"alias" => "dispensing_fee",
				"rules" => ["numeric"],
			],
			"unitPrice" => [
				"type" => Type::float(),
				"description" =>
					"The configured sale price for the item at the time it was added to the invoice",
				"alias" => "unit_price",
				"rules" => ["numeric"],
			],
			"isNonTaxable" => [
				"type" => Type::boolean(),
				"description" => "Should this item be taxed",
				"alias" => "is_non_taxable",
				"rules" => [],
			],
			"applyToRemainder" => [
				"type" => Type::boolean(),
				"description" => "Should this applied to remainder",
				"alias" => "apply_discount_to_remainder",
				"rules" => [],
			],
			"reminderIntervalId" => [
				"type" => Type::int(),
				"description" => "Reminder Interval id",
				"relation" => true,
				"default" => null,
				"alias" => "reminder_interval_id",
			],
			"reminderReplaces" => [
				"type" => Type::listOf(
					GraphQL::type("ItemReminderReplacesInput"),
				),
				"description" => "List of reminders this replaces",
				"rules" => [],
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
				"rules" => [],
			],
			"nextTagNumber" => [
				"type" => Type::int(),
				"description" =>
					"The next tag id to use when an item of this type is sold",
				"alias" => "next_tag_number",
				"rules" => [],
			],
			"drugIdentifier" => [
				"type" => Type::string(),
				"description" =>
					"National Drug Id for any controlled substances",
				"alias" => "drug_identifier",
				"rules" => [],
			],
			"isSingleLineKit" => [
				"type" => Type::boolean(),
				"description" =>
					"Display Item Kit as a single line on invoices",
				"alias" => "is_single_line_kit",
				"rules" => [],
			],
			"itemSpeciesRestrictions" => [
				"type" => Type::listOf(
					GraphQL::type("ItemSpeciesRestrictionInput"),
				),
				"description" => "List of species restrictions for this item",
			],
			"itemLocations" => [
				"type" => Type::listOf(GraphQL::type("ItemLocationInput")),
				"description" => "List of locations",
			],
			"itemTaxes" => [
				"type" => Type::listOf(GraphQL::type("ItemTaxInput")),
				"description" => "List of Item Taxes",
				"rules" => [],
			],
			"itemVolumePricing" => [
				"type" => Type::listOf(GraphQL::type("ItemVolumePricingInput")),
				"description" => "List of Volume Pricing",
			],
			"itemKitItems" => [
				"type" => Type::listOf(GraphQL::type("ItemKitItemsInput")),
				"description" => "List of items in a kit",
			],
			"manufacturerId" => [
				"type" => Type::int(),
				"description" => "Who makes this thing",
				"alias" => "manufacturer_id",
				"rules" => [],
			],
		];
	}
}
