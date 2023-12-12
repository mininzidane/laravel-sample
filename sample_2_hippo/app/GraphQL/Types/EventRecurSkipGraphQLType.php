<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\EventRecurField;
use App\Models\EventRecurSkip;
use GraphQL\Type\Definition\Type;

class EventRecurSkipGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "eventRecurSkip";

	protected $attributes = [
		"name" => "EventRecurSkip",
		"description" =>
			"Information about exceptions to the standard recurrence rule for an appointment",
		"model" => EventRecurSkip::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"time" => [
				"type" => Type::string(),
				"description" =>
					"The appointment time to omit from the recurrence pattern",
				"alias" => "start_time",
			],
			"recurrence" => (new EventRecurField([
				"description" => "The associated recurrence",
			]))->toArray(),
		];
	}
}
