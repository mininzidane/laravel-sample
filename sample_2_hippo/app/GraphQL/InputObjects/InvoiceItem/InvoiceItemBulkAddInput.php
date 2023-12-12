<?php

namespace App\GraphQL\InputObjects\InvoiceItem;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceItemGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceItemBulkAddInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceItemBulkAddInput",
		"description" =>
			"The input object for creating a new invoice item for multiple invoices",
	];

	protected $graphQLType = InvoiceItemGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"invoiceIds" => [
				"type" => Type::listOf(Type::int()),
				"description" =>
					"The ids of the invoices to have an item added to",
				"default" => null,
				"rules" => ["required"],
			],
			"item" => [
				"type" => Type::int(),
				"description" => "The id of the item to be added",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Item,id",
				],
			],
			"administeredDate" => [
				"type" => Type::string(),
				"description" => "The date the item was administered",
				"rules" => ["date"],
			],
			"provider" => [
				"type" => Type::int(),
				"description" =>
					"The id of the provider that was responsible for administering the item",
				"rules" => ["exists:" . $subdomainName . "App\Models\User,id"],
				"default" => null,
			],
			"quantity" => [
				"type" => Type::int(),
				"description" =>
					"The quantity of the item to add to the selected invoices",
				"rules" => ["integer", "min:1", "required"],
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
