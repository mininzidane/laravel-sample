<?php

namespace App\GraphQL\InputObjects;

use App\GraphQL\Types\ClearentTerminalGraphQLType;
use GraphQL\Type\Definition\Type;

class ClearentTerminalUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "clearentTerminalUpdateInput",
		"description" =>
			"The input object for updating an existing clearent terminal",
	];

	protected $graphQLType = ClearentTerminalGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"id" => [
				"type" => Type::int(),
				"description" => "The id of the clearent terminal to update",
				"default" => null,
				"rules" => [
					"required",
					"exists:" .
					$subdomainName .
					"App\Models\ClearentTerminal,id",
				],
			],
			"location" => [
				"type" => Type::int(),
				"description" =>
					"The id of the location to assign to this clearent terminal",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Location,id",
				],
				"alias" => "location_id",
			],
			"terminalId" => [
				"type" => Type::string(),
				"description" => "The id for the Clearent terminal",
				"rules" => ["max:30"],
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
			],
		];
	}
}
