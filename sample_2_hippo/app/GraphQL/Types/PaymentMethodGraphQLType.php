<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\PaymentField;
use App\GraphQL\Fields\PaymentPlatformField;
use App\Models\PaymentMethod;
use GraphQL\Type\Definition\Type;

class PaymentMethodGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "paymentMethod";

	protected $attributes = [
		"name" => "PaymentMethod",
		"description" => "An available payment method",
		"model" => PaymentMethod::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" =>
					"The human-readable name of the payment method",
			],
			"protected" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for indicating whether the payment method is protected or not",
			],
			"active" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the payment method is enable for this organization",
			],
			"userFacing" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the payment method should be directly selectable",
				"alias" => "user_facing",
			],
			"processType" => [
				"type" => Type::string(),
				"description" =>
					"The type of processing to be used for the chosen payment method",
				"alias" => "process_type",
			],
			"paymentPlatform" => (new PaymentPlatformField([
				"description" =>
					"The payment platforms associated with this payment method",
			]))->toArray(),
			"payments" => (new PaymentField([
				"isList" => true,
				"description" => "The payments made with this payment method",
			]))->toArray(),
		];
	}
}
