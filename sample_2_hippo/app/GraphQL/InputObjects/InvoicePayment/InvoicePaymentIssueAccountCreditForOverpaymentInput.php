<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentIssueAccountCreditForOverpaymentInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentIssueAccountCreditForOverpaymentInput",
		"description" =>
			"The input object for issuing an account credit for an invoice overpayment",
	];

	protected $graphQLType = InvoicePaymentGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"invoice" => [
				"type" => Type::int(),
				"description" =>
					"The id of the invoice to assign to this invoice payment",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Invoice,id",
				],
			],
			"paymentDate" => [
				"type" => Type::string(),
				"description" =>
					"The date the payment should use as the received date",
				"rules" => ["date"],
			],
			"locationId" => [
				"type" => Type::int(),
				"description" => "The location where payment is created",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Location,id",
				],
			],
		];
	}
}
