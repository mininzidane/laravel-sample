<?php

namespace App\GraphQL\InputObjects\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceSaveDetailsInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceSaveDetailsInput",
		"description" =>
			"The input object for setting the active invoice by id",
	];

	protected $graphQLType = InvoiceGraphQLType::class;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the Invoice",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Invoice,id",
				],
			],
			"comment" => [
				"type" => Type::string(),
				"description" =>
					"A comment describing any additional information for this invoice",
			],
			"isTaxable" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether taxes are applied to this invoice",
				"alias" => "is_taxable",
			],
			"isEstimate" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the invoice should be considered an estimate",
				"default" => null,
				"alias" => "is_estimate",
				"rules" => ["required"],
			],
			"printComment" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether the comment is printed on invoices",
				"alias" => "print_comment",
			],
		];
	}
}
