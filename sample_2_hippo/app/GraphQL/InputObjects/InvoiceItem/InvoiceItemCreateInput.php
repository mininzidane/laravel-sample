<?php

namespace App\GraphQL\InputObjects\InvoiceItem;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceItemGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceItemCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceItemCreateInput",
		"description" => "The input object for creating a new invoice item",
	];

	protected $graphQLType = InvoiceItemGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"item" => [
				"type" => Type::int(),
				"description" => "The id of the item to add to the invoice",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Item,id",
				],
			],
			"invoice" => [
				"type" => Type::int(),
				"description" =>
					"The id of the invoice to assign to this invoice item",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Invoice,id",
				],
			],
			"provider" => [
				"type" => Type::int(),
				"description" =>
					"The id of the provider to assign to this invoice item",
			],
			"chart" => [
				"type" => Type::int(),
				"description" =>
					"The id of the chart to assign to this invoice item",
				"default" => null,
			],
			"chartType" => [
				"type" => Type::string(),
				"description" => "The type of chart to be associated",
				"default" => null,
			],
			"quantity" => [
				"type" => Type::float(),
				"description" => "The quantity of this invoice item",
				"default" => 1,
			],
			"administeredDate" => [
				"type" => Type::string(),
				"description" =>
					"The related date this invoice item was administered.",
				"default" => null,
			],
			"allowExcessiveQuantity" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not quantities that would result in negative totals are allowed",
				"default" => 0,
			],
		];
	}
}
