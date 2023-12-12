<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ClearentTransactionField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\PaymentPlatformField;
use App\Models\ClearentTerminal;
use GraphQL\Type\Definition\Type;

class ClearentTerminalGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "clearentTerminal";

	protected $attributes = [
		"name" => "ClearentTerminal",
		"description" => "An available Clearent terminal",
		"model" => ClearentTerminal::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"terminalId" => [
				"type" => Type::string(),
				"description" => "The id for the Clearent terminal",
				"rules" => ["max:30"],
				"alias" => "terminal_id",
			],
			"name" => [
				"type" => Type::string(),
				"description" =>
					"The human-readable name of the payment method",
				"rules" => ["max:191"],
			],
			"apiKey" => [
				"type" => Type::string(),
				"description" => "The API Key for use with this terminal",
				"rules" => ["max:128"],
				"alias" => "api_key",
			],
			"paymentPlatform" => (new PaymentPlatformField([
				"description" =>
					"The payment platform configured for this terminal",
			]))->toArray(),
			"location" => (new LocationField([
				"description" =>
					"The location that this terminal is configured for",
			]))->toArray(),
			"clearentTransactions" => (new ClearentTransactionField([
				"isList" => true,
				"description" =>
					"The clearent transactions associated with the terminal",
			]))->toArray(),
		];
	}
}
