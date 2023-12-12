<?php

namespace App\GraphQL\InputObjects\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceCompleteInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceCompleteInput",
		"description" => "The input object for completing an invoice by id",
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
		];
	}
}
