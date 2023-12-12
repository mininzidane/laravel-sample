<?php

namespace App\GraphQL\InputObjects\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceReopenInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceReopenInput",
		"description" =>
			"The input object for reopening an invoice by id for a patient",
	];

	protected $graphQLType = InvoiceGraphQLType::class;

	protected $inputObject = true;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"invoiceId" => [
				"type" => Type::id(),
				"description" => "The id of the invoice to reopen",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Invoice,id",
				],
			],
		];
	}
}
