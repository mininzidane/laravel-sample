<?php

namespace App\GraphQL\Types;

use App\Models\PaymentType;
use GraphQL\Type\Definition\Type;

class PaymentTypeGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "paymentType";

	protected $attributes = [
		"name" => "PaymentType",
		"description" => "The available payment types used by the subdomain",
		"model" => PaymentType::class,
	];

	public function columns(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the payment type",
				"alias" => "payment_type",
			],
		];
	}
}
