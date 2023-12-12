<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentCancelClearentInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentCancelClearentInput",
		"description" =>
			"The input object for creating a new invoice payment via clearent",
	];

	protected $graphQLType = InvoicePaymentGraphQLType::class;

	public function fields(): array
	{
		return [
			"invoicePaymentIds" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The ids of the invoice payments to cancel",
				"default" => null,
				"rules" => ["required"],
			],
		];
	}
}
