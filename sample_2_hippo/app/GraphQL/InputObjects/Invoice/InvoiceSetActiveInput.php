<?php

namespace App\GraphQL\InputObjects\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceSetActiveInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceSetActiveInput",
		"description" =>
			"The input object for setting the active invoice by id for a patient",
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
			"invoiceId" => [
				"type" => Type::id(),
				"description" => "The id of the invoice to set active",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Invoice,id",
				],
			],
		];
	}
}
