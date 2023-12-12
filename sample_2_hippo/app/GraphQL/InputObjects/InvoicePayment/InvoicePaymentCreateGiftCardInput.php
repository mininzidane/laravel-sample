<?php

namespace App\GraphQL\InputObjects\InvoicePayment;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoicePaymentGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoicePaymentCreateGiftCardInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoicePaymentCreateGiftCardInput",
		"description" =>
			"The input object for creating a new account credit invoice payment",
	];

	protected $graphQLType = InvoicePaymentGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"invoiceIds" => [
				"type" => Type::listOf(Type::int()),
				"description" => "The invoices to apply payment to",
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
			"amountTendered" => [
				"type" => Type::float(),
				"description" =>
					"The quantity paid for the services and items rendered",
				"default" => 0,
				"rules" => ["numeric", "min:0"],
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
			"giftCard" => [
				"type" => Type::int(),
				"description" => "The id of the gift card to use",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Credit,id",
				],
			],
			"useFullCreditAmount" => [
				"type" => Type::boolean(),
				"description" =>
					"When using credit, whether or not the full value of the credit should be used",
				"default" => true,
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
