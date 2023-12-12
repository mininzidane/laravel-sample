<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\TaxField;
use App\Models\InvoiceItemTax;
use GraphQL\Type\Definition\Type;

class InvoiceItemTaxGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "invoiceItemTax";

	protected $attributes = [
		"name" => "InvoiceItemTax",
		"description" => "The tax information for a given invoice item",
		"model" => InvoiceItemTax::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The descriptive name of the tax",
				"rules" => ["max:191"],
			],
			"percent" => [
				"type" => Type::float(),
				"description" => "The tax percentage to be charged",
			],
			"amount" => [
				"type" => Type::float(),
				"description" => "Calculated amount of the tax",
			],
			"invoiceItem" => (new InvoiceItemField([
				"description" => "The invoice item that this tax is applied to",
			]))->toArray(),
			"tax" => new TaxField([
				"description" => "The tax associated with this item",
			]),
		];
	}
}
