<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemLegacyField;
use App\GraphQL\Fields\ItemTaxLegacyField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\SaleField;
use App\GraphQL\Fields\UserField;
use App\Models\LineItem;
use GraphQL\Type\Definition\Type;

class LineItemGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "lineItem";

	protected $attributes = [
		"name" => "LineItem",
		"description" => "A sale line item",
		"model" => LineItem::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the sale line item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"description" => [
				"type" => Type::string(),
				"description" => "Any available description of the line item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" =>
					"The serial number associated with a prescription if one exists",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
				"alias" => "serial_number",
			],
			"line" => [
				"type" => Type::int(),
				"description" => "Line position on the sale",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"quantity" => [
				"type" => Type::string(),
				"description" => "Quantity of the given item purchased",
				"alias" => "quantity_purchased",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"costPrice" => [
				"type" => Type::string(),
				"description" => "The base cost of the item sold",
				"alias" => "item_cost_price",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"unitPrice" => [
				"type" => Type::string(),
				"description" => "The final cost of the item sold",
				"alias" => "item_unit_price",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"discountPercentage" => [
				"type" => Type::string(),
				"description" =>
					"The percent discount applied to the line item",
				"alias" => "discount_percent",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"dispensingFee" => [
				"type" => Type::string(),
				"description" => "The fee associated with dispensing this item",
				"alias" => "dispensing_fee",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"receivingItemExpirationDate" => [
				"type" => Type::string(),
				"description" =>
					"The expiration date for this batch of the given item",
				"alias" => "receiving_item_expiration_date",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"receivingItemLotNumber" => [
				"type" => Type::string(),
				"description" =>
					"The lot number for this batch of the given item",
				"alias" => "receiving_item_lot_number",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"total" => [
				"type" => Type::string(),
				"description" => "The total price for the line item",
				"alias" => "item_line_total",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"patient" => (new PatientField([
				"description" => "The recipient of this medication",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			]))->toArray(),
			"seenBy" => (new UserField([
				"description" => "The vet that saw the patient",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			]))->toArray(),
			"taxes" => (new ItemTaxLegacyField([
				"description" => "The tax percent associated line item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
				"isList" => true,
			]))->toArray(),
			"sale" => (new SaleField([
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			]))->toArray(),
			"item" => (new ItemLegacyField([
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "Items Object". The LineItem Object is only used for Hippo 1 users on api.hippo.vet.',
			]))->toArray(),
		];
	}
}
