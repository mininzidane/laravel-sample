<?php

namespace App\GraphQL\InputObjects\InvoiceItem;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceItemGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceItemDeleteInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceItemDeleteInput",
		"description" => "The input object for creating a new invoice item",
	];

	protected $graphQLType = InvoiceItemGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"invoiceItem" => [
				"type" => Type::int(),
				"description" => "The id of the invoice item to delete",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\InvoiceItem,id",
				],
			],
		];
	}
}
