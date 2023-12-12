<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemTaxLegacyField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\PrescriptionField;
use App\GraphQL\Fields\ReceivingItemLegacyField;
use App\GraphQL\Fields\ReminderField;
use App\GraphQL\Fields\SupplierLegacyField;
use App\GraphQL\Fields\VaccinationField;
use App\Models\ItemLegacy;
use GraphQL\Type\Definition\Type;

class ItemLegacyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemLegacy";

	protected $attributes = [
		"name" => "ItemLegacy",
		"description" => "An inventory item",
		"model" => ItemLegacy::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item",
				"alias" => "item_id",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"category" => [
				"type" => Type::string(),
				"description" => "Item category",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"description" => [
				"type" => Type::string(),
				"description" => "Item description",
				"rules" => ["min:1"],
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"costPrice" => [
				"type" => Type::string(),
				"description" => "Item cost price",
				"alias" => "cost_price",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"unitPrice" => [
				"type" => Type::string(),
				"description" => "Item unit price",
				"alias" => "unit_price",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"itemNumber" => [
				"type" => Type::string(),
				"description" => "Item description",
				"alias" => "item_number",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"quantity" => [
				"type" => Type::string(),
				"description" => "",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"reorderLevel" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "reorder_level",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"serialized" => [
				"type" => Type::boolean(),
				"description" => "",
				"alias" => "is_serialized",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"procedure" => [
				"type" => Type::boolean(),
				"description" => "Item is a procedure",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"vaccine" => [
				"type" => Type::boolean(),
				"description" => "",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"prescription" => [
				"type" => Type::boolean(),
				"description" => "",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"controlledSubstance" => [
				"type" => Type::boolean(),
				"description" => "",
				"alias" => "controlled_substance",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"nonStocking" => [
				"type" => Type::boolean(),
				"description" => "",
				"alias" => "non_stocking",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"discountCode" => [
				"type" => Type::boolean(),
				"description" => "",
				"alias" => "discount_code",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"lotNumber" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "lot_number",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"rabiesTag" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "rabies_tag",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "expiration_date",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"dispensingFee" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "dispensing_fee",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"euthanasia" => [
				"type" => Type::boolean(),
				"description" => "",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"reproductive" => [
				"type" => Type::boolean(),
				"description" => "",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"labTest" => [
				"type" => Type::boolean(),
				"description" => "",
				"alias" => "labtest",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"discountRemainder" => [
				"type" => Type::boolean(),
				"description" => "",
				"alias" => "discount_remainder",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"costPercentage" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "cost_percentage",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"minimumSaleAmount" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "min_sale_amount",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"discountQuantities" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "discount_qtys",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"discountQuantitiesPrice" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "discount_qtys_price",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"reminder" => [
				"type" => Type::string(),
				"description" => "",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			],
			"itemTaxes" => (new ItemTaxLegacyField([
				"isList" => true,
				"description" => "Taxes associated with this item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
			"receivingItems" => (new ReceivingItemLegacyField([
				"isList" => true,
				"description" =>
					"The receiving lines associated with this item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
			"prescriptions" => (new PrescriptionField([
				"isList" => true,
				"description" => "The prescriptions associated with this item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
			"reminders" => (new ReminderField([
				"isList" => true,
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
			"location" => (new LocationField([
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
			"organization" => (new OrganizationField([
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
			"supplier" => (new SupplierLegacyField([
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
			"vaccinations" => (new VaccinationField([
				"isList" => true,
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The ItemLegacy Object is only used for Hippo 1 users.',
			]))->toArray(),
		];
	}
}
