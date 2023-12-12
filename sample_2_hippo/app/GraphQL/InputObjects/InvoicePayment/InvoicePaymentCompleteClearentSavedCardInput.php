<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentCompleteClearentSavedCardInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentCompleteClearentSavedCardInput",
		"description" =>
			"The input object for creating a new invoice payment via clearent with a saved card",
	];

	protected $graphQLType = InvoicePaymentGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"amountTendered" => [
				"type" => Type::float(),
				"description" =>
					"The quantity paid for the services and items rendered",
				"default" => 0,
				"rules" => ["required", "numeric"],
			],
			"invoicePaymentIds" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The ids of the invoice payments to complete",
				"default" => null,
				"rules" => ["required"],
			],
			"paymentMethod" => [
				"type" => Type::int(),
				"description" =>
					"The id of the payment method used to create this invoice payment",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\PaymentMethod,id",
				],
			],
			"paymentPlatform" => [
				"type" => Type::int(),
				"description" =>
					"The id for the payment platform used to provide payment",
				"default" => null,
				"rules" => [
					"required",
					"exists:" .
					$subdomainName .
					"App\Models\PaymentPlatform,id",
				],
			],
			"owner" => [
				"type" => Type::int(),
				"description" =>
					"The id of the owner associated with this payment",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Owner,id",
				],
			],
			"tokenId" => [
				"type" => Type::int(),
				"description" => "The ID of the Clearent saved card token",
				"default" => null,
			],
			"paymentDate" => [
				"type" => Type::string(),
				"description" =>
					"The date the payment should use as the received date",
				"rules" => ["date"],
			],
		];
	}
}
