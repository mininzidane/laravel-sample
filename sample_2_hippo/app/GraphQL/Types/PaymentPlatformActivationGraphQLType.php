<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\PaymentPlatformField;
use App\Models\PaymentPlatformActivation;
use GraphQL\Type\Definition\Type;

class PaymentPlatformActivationGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "paymentPlatformActivation";

	protected $attributes = [
		"name" => "PaymentPlatformActivation",
		"description" =>
			"A record of the activation of a point of sale payment platform",
		"model" => PaymentPlatformActivation::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"mode" => [
				"type" => Type::string(),
				"description" =>
					"What mode the payment platform is current set to",
			],
			"info" => [
				"type" => Type::string(),
				"description" =>
					"Descriptive text indicating any additional information about the platform",
			],
			"isActive" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for indicating whether the payment platform is enabled or not",
				"alias" => "is_active",
			],
			"paymentPlatform" => (new PaymentPlatformField([
				"description" =>
					"The payment platform this platform activation is for",
			]))->toArray(),
			"location" => (new LocationField([
				"description" => "The location for this payment platform",
			]))->toArray(),
		];
	}
}
