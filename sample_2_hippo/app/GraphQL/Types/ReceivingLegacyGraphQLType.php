<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\ReceivingItemLegacyField;
use App\GraphQL\Fields\SupplierLegacyField;
use App\GraphQL\Fields\UserField;
use App\Models\ReceivingLegacy;
use GraphQL\Type\Definition\Type;

class ReceivingLegacyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "receivingLegacy";

	protected $attributes = [
		"name" => "ReceivingLegacy",
		"description" => "Inventory Receiving",
		"model" => ReceivingLegacy::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the receiving",
				"alias" => "receiving_id",
			],
			"description" => [
				"type" => Type::string(),
				"description" => "Any remarkable details about the receiving",
				"alias" => "comment",
			],
			"paymentType" => [
				"type" => Type::string(),
				"description" =>
					"The payment type used to purchase the items in this receiving",
				"alias" => "payment_type",
			],

			"supplier" => (new SupplierLegacyField([
				"description" =>
					"The supplier the items in this receiving were purchased from",
			]))->toArray(),
			"user" => (new UserField([
				"description" => "The user that ran this receiving",
			]))->toArray(),
			"location" => (new LocationField([
				"description" => "The location where the items were received",
			]))->toArray(),
			"organization" => (new OrganizationField([
				"description" =>
					"The organization purchasing the items in the receiving",
			]))->toArray(),
			"receivingItems" => (new ReceivingItemLegacyField([
				"isList" => true,
				"description" => "The items included in this receiving",
			]))->toArray(),
		];
	}
}
