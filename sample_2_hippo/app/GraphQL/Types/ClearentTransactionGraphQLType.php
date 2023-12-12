<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ClearentTerminalField;
use App\GraphQL\Fields\ClearentTokenField;
use App\GraphQL\Fields\PaymentField;
use App\GraphQL\Fields\PaymentPlatformField;
use App\GraphQL\Fields\UserField;
use App\Models\ClearentTransaction;
use GraphQL\Type\Definition\Type;

class ClearentTransactionGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "clearentTransaction";

	protected $attributes = [
		"name" => "ClearentTransaction",
		"description" => "A saved Clearent transaction",
		"model" => ClearentTransaction::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"requestId" => [
				"type" => Type::string(),
				"description" =>
					"The id associated with this Clearent transaction",
				"alias" => "request_id",
			],
			"requestType" => [
				"type" => Type::string(),
				"description" =>
					"The type of request that this transaction encompasses",
				"alias" => "request_type",
			],
			"requestBody" => [
				"type" => Type::string(),
				"description" =>
					"The request sent to Clearent as part of this transaction",
				"alias" => "request_body",
			],
			"responseStatus" => [
				"type" => Type::string(),
				"description" =>
					"The 3 character status code received as part of the Clearent response",
				"alias" => "response_status",
				"rules" => ["size:3"],
			],
			"cardType" => [
				"type" => Type::string(),
				"description" =>
					"The type of credit card used in the transcation",
				"alias" => "card_type",
			],
			"lastFourDigits" => [
				"type" => Type::string(),
				"description" =>
					"The last four digits of the credit card number used in the transaction",
				"alias" => "last_four_digits",
			],
			"authorizationCode" => [
				"type" => Type::string(),
				"description" => "The authorization code of the transcation",
				"alias" => "authorization_code",
			],
			"responseBody" => [
				"type" => Type::string(),
				"description" =>
					"The response received from Clearent as part of this transaction",
				"alias" => "response_body",
			],
			"platformMode" => [
				"type" => Type::string(),
				"description" => "The platform mode, either prod or test",
				"alias" => "platform_mode",
			],
			"paymentPlatform" => (new PaymentPlatformField([
				"description" =>
					"Which payment platform was used to make this transaction",
			]))->toArray(),
			"clearentTerminal" => (new ClearentTerminalField([
				"description" =>
					"Which Clearent terminal this transaction originated from",
			]))->toArray(),
			"user" => (new UserField([
				"description" => "Which user ran this transaction",
			]))->toArray(),
			"payment" => (new PaymentField([
				"description" =>
					"The payment that was made via this transaction",
			]))->toArray(),
			"token" => (new ClearentTokenField([
				"description" =>
					"The Clearent token representing this transaction",
			]))->toArray(),
		];
	}
}
