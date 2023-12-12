<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\LogActionField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\UserField;
use App\Models\Log;
use GraphQL\Type\Definition\Type;

class LogGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "logs";

	protected $attributes = [
		"name" => "Log",
		"description" => "Activity Log",
		"model" => Log::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "Id of the log entry",
			],
			"organization" => (new OrganizationField([
				"description" => "The associated organization",
			]))->toArray(),
			"location" => (new LocationField([
				"description" =>
					"The location that this terminal is configured for",
			]))->toArray(),
			"user" => (new UserField([
				"description" => "The user that created this log item",
			]))->toArray(),
			"actions" => (new LogActionField([
				"description" => "The action associated with this entry",
			]))->toArray(),
			"actionId" => [
				"type" => Type::string(),
				"description" => "Id of the affected item",
				"alias" => "action_id",
			],
			"affected_id" => [
				"type" => Type::int(),
				"description" => "Id of the affected item",
			],
			"timestamp" => [
				"type" => Type::string(),
				"description" => "When the log was created",
				"rules" => ["date"],
			],
		];
	}
}
