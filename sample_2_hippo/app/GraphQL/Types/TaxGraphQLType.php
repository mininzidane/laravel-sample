<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceItemTaxField;
use App\GraphQL\Fields\ItemField;
use App\Models\Tax;
use GraphQL\Type\Definition\Type;

class TaxGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "tax";

	protected $attributes = [
		"name" => "Tax",
		"description" => "The tax information for a given invoice item",
		"model" => Tax::class,
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
				"rules" => ["max:255"],
			],
			"percent" => [
				"type" => Type::float(),
				"description" => "The percent rate of the tax",
				"rules" => ["min:0"],
			],
			"relationshipNumber" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The category relations to other tables",
				"alias" => "relationship_number",
			],
			"items" => new ItemField([
				"isList" => true,
				"description" => "The items configured to use this tax",
			]),
			"invoiceItemTaxes" => new InvoiceItemTaxField([
				"isList" => true,
				"description" => "The invoice item taxes based off of this tax",
			]),
		];
	}
}
