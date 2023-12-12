<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\InvoiceItemField;
use App\Models\InvoiceAppliedDiscount;
use GraphQL\Type\Definition\Type;

class InvoiceAppliedDiscountGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "invoiceAppliedDiscount";

	protected $attributes = [
		"name" => "InvoiceAppliedDiscount",
		"description" => "Details for a given discount application",
		"model" => InvoiceAppliedDiscount::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"amountApplied" => [
				"type" => Type::float(),
				"description" => "The amount of the discount applied",
				"rules" => ["numeric"],
				"alias" => "amount_applied",
			],
			"invoice" => (new InvoiceField())->toArray(),
			"discountInvoiceItem" => (new InvoiceItemField([
				"description" => "The items included in this invoice",
			]))->toArray(),
			"discountApplications" => (new InvoiceItemField([
				"description" => "The items included in this invoice",
			]))->toArray(),
		];
	}
}
