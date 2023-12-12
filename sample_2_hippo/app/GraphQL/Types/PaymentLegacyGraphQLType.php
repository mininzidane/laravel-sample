<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\SaleField;
use App\Models\PaymentLegacy;
use GraphQL\Type\Definition\Type;

class PaymentLegacyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "paymentLegacy";

	protected $attributes = [
		"name" => "PaymentLegacy",
		"description" => "A payment for a sale",
		"model" => PaymentLegacy::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "Id for the sale",
				"alias" => "payment_id",
			],
			"type" => [
				"type" => Type::string(),
				"description" => "The payment form provided",
				"alias" => "payment_type",
			],
			"amount" => [
				"type" => Type::string(),
				"description" => "The amount tendered",
				"alias" => "payment_amount",
			],
			"sale" => (new SaleField())->toArray(),
		];
	}
}
