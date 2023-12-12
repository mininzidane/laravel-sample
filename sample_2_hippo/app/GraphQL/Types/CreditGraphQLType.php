<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\OwnerField;
use App\GraphQL\Fields\PaymentField;
use App\GraphQL\Fields\InvoiceItemField;
use App\Models\Credit;
use GraphQL\Type\Definition\Type;

class CreditGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "credit";

	protected $attributes = [
		"name" => "Credit",
		"description" => "A credit voucher",
		"model" => Credit::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the credit",
				"alias" => "id",
			],
			"number" => [
				"type" => Type::string(),
				"description" => "The unique credit voucher number",
				"alias" => "number",
				"rules" => [],
			],
			"type" => [
				"type" => Type::string(),
				"description" => "Type of card",
				"alias" => "type",
				"rules" => ["required", "in:Account Credit,Gift Card"],
			],
			"currentValue" => [
				"type" => Type::float(),
				"description" => "The current balance of the credit voucher",
				"alias" => "value",
				"rules" => ["gte:0"],
			],
			"originalValue" => [
				"type" => Type::float(),
				"description" =>
					"The value of the credit voucher when first purchased",
				"alias" => "original_value",
				"rules" => ["gte:0"],
			],
			"updatedAt" => [
				"type" => Type::string(),
				"description" => "Date last updated",
				"alias" => "updated_at",
			],
			"organization" => (new OrganizationField([
				"description" => "The associated organization",
			]))->toArray(),
			"invoiceItem" => (new InvoiceItemField([
				"description" => "The invoice item that created this credit",
			]))->toArray(),
			"payments" => (new PaymentField([
				"isList" => true,
				"description" => "The payments associated with this credit",
			]))->toArray(),
			"owner" => (new OwnerField())->toArray(),
		];
	}
}
