<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentDeleteInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentDeleteInput",
		"description" => "The input object for creating a new invoice payment",
	];

	protected $graphQLType = InvoicePaymentGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"invoicePayment" => [
				"type" => Type::int(),
				"description" => "The id of the invoice payment to delete",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\InvoicePayment,id",
				],
			],
		];
	}
}
