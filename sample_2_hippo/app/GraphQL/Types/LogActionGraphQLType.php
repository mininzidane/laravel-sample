<?php

namespace App\GraphQL\Types;

use App\Models\LogAction;
use GraphQL\Type\Definition\Type;

class LogActionGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "logActions";

	protected $attributes = [
		"name" => "LogAction",
		"description" => "Log Actions",
		"model" => LogAction::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "Id of the log entry",
			],
			"action" => [
				"type" => Type::string(),
				"description" => "The name of the action",
			],
		];
	}
}
