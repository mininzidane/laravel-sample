<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentCreateStandardInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentCreateStandardInput",
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
			"invoiceIds" => [
				"type" => Type::listOf(Type::int()),
				"description" =>
					"The id of the invoice to assign to this invoice payment",
				"default" => null,
				"rules" => ["required"],
			],
			"paymentOrder" => [
				"type" => Type::int(),
				"description" =>
					"Which ordering type should be used to apply bulk payments",
				"default" => 0,
				"rules" => ["numeric", "min:0", "max:1"],
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
