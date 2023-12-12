<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\GraphQL\Fields\EventRecurSkipField;
use App\Models\EventDays;
use GraphQL\Type\Definition\Type;

class EventDaysGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "eventDays";

	protected $attributes = [
		"name" => "EventDays",
		"description" =>
			"Appointment recurrence information by individual days",
		"model" => EventDays::class,
	];

	public function columns(): array
	{
		return [
			"event_id" => [
				"type" => Type::int(),
				"description" => "The id of the associated scheduled event",
			],
			"day" => [
				"type" => Type::int(),
				"description" =>
					"Hippo-Specific: Which days for the event to occur on",
			],
			"dayOfWeekAbbreviation" => [
				"type" => Type::string(),
				"description" => "The abbreviation for the day of the week",
				"selectable" => false,
				"resolve" => function ($root) {
					return $root->dayOfWeekAbbreviation;
				},
			],
		];
	}
}
