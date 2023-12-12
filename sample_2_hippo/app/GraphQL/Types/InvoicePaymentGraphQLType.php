<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\PaymentField;
use App\Models\InvoicePayment;
use GraphQL\Type\Definition\Type;

class InvoicePaymentGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "invoicePayment";

	protected $attributes = [
		"name" => "InvoicePayment",
		"description" => "Details of a payment for an invoice",
		"model" => InvoicePayment::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"amountApplied" => [
				"type" => Type::float(),
				"description" =>
					"The amount of the payment that is applied to the associated invoice",
				"alias" => "amount_applied",
			],
			"invoice" => (new InvoiceField([
				"description" =>
					"The invoice in this invoice-payment combination",
			]))->toArray(),
			"payment" => (new PaymentField([
				"description" =>
					"The payment in this invoice-payment combination",
			]))->toArray(),
		];
	}
}
