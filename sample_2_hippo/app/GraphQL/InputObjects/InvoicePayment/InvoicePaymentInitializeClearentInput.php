<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentInitializeClearentInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentInitializeClearentInput",
		"description" =>
			"The input object for initializing a new invoice payment via clearent",
	];

	protected $graphQLType = InvoicePaymentGraphQLType::class;

	public function fields(): array
	{
		return [
			"invoiceIds" => [
				"type" => Type::listOf(Type::int()),
				"description" =>
					"The invoice ids to create pending invoice payments for",
				"rules" => ["required"],
			],
			"amountTendered" => [
				"type" => Type::float(),
				"description" =>
					"The payment amount to verify does not exceed the current invoice amount due",
				"rules" => ["required", "numeric"],
			],
		];
	}
}
