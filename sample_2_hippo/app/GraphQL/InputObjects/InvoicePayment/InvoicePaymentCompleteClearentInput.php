<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentCompleteClearentInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentCompleteClearentInput",
		"description" =>
			"The input object for creating a new invoice payment via clearent",
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
			"requestId" => [
				"type" => Type::string(),
				"description" =>
					"The request id associated with this transaction",
				"rules" => ["required"],
			],
			"requestType" => [
				"type" => Type::string(),
				"description" =>
					"The request type associated with this transaction",
				"rules" => ["required"],
			],
			"responseStatus" => [
				"type" => Type::string(),
				"description" =>
					"The response status code associated with this transaction",
				"rules" => ["required"],
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
			"platformMode" => [
				"type" => Type::string(),
				"description" =>
					"The mode the request was made in (TEST or PROD)",
				"rules" => ["required"],
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
			"clearentTerminal" => [
				"type" => Type::int(),
				"description" =>
					"The id of the Clearent terminal with this payment",
				"default" => null,
				"rules" => [
					"required",
					"exists:" .
					$subdomainName .
					"App\Models\ClearentTerminal,id",
				],
			],
			"tokenId" => [
				"type" => Type::string(),
				"description" => "The ID of the Clearent saved card token",
				"default" => null,
			],
			"tokenCardType" => [
				"type" => Type::string(),
				"description" =>
					"The card type of the Clearent saved card token",
				"default" => null,
			],
			"tokenLastFour" => [
				"type" => Type::string(),
				"description" =>
					"The last four digits of the Clearent saved card token",
				"default" => null,
			],
			"tokenExpDate" => [
				"type" => Type::string(),
				"description" =>
					"The card expiration of the Clearent saved card token",
				"default" => null,
			],
			"tokenName" => [
				"type" => Type::string(),
				"description" => "The name of the Clearent saved card token",
				"default" => null,
			],
			"usedTokenId" => [
				"type" => Type::int(),
				"description" =>
					"The ID of the Saved Card Token used for the payment",
				"default" => null,
			],
			"terminalId" => [
				"type" => Type::string(),
				"description" => "The clearent provided terminal id",
				"rules" => ["required"],
			],
			"response" => [
				"type" => Type::string(),
				"description" =>
					"The response received from Clearent for this transaction",
				"rules" => ["required"],
			],
			"request" => [
				"type" => Type::string(),
				"description" =>
					"The request sent to Clearent for this transaction",
				"rules" => ["required"],
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
