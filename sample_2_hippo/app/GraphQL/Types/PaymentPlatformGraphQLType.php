<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ClearentTerminalField;
use App\GraphQL\Fields\ClearentTransactionField;
use App\GraphQL\Fields\PaymentField;
use App\GraphQL\Fields\PaymentMethodField;
use App\GraphQL\Fields\PaymentPlatformActivationField;
use App\Models\PaymentPlatform;
use GraphQL\Type\Definition\Type;

class PaymentPlatformGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "paymentPlatform";

	protected $attributes = [
		"name" => "PaymentPlatform",
		"description" =>
			"A payment platform for use in the point of sale system",
		"model" => PaymentPlatform::class,
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
				"description" => "Human-readable name of the payment platform",
				"rules" => ["max:191"],
			],
			"paymentPlatformActivations" => (new PaymentPlatformactivationField(
				[
					"isList" => true,
					"description" =>
						"The activations for this payment platform",
				],
			))->toArray(),
			"paymentMethods" => (new PaymentMethodField([
				"isList" => true,
				"description" =>
					"The payment methods associated with this payment platform",
			]))->toArray(),
			"clearentTerminals" => (new ClearentTerminalField([
				"isList" => true,
				"description" =>
					"The Clearent terminals configured for this payment platform",
			]))->toArray(),
			"clearentTransactions" => (new ClearentTransactionField([
				"isList" => true,
				"description" =>
					"The Clearent transactions for this payment platform",
			]))->toArray(),
			"payments" => (new PaymentField([
				"isList" => true,
				"description" => "The payments made with this payment platform",
			]))->toArray(),
		];
	}
}
