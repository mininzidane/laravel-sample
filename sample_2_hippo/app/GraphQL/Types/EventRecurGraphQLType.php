<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\GraphQL\Fields\EventDaysField;
use App\GraphQL\Fields\EventRecurSkipField;
use App\Models\EventRecur;
use GraphQL\Type\Definition\Type;

class EventRecurGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "eventRecur";

	protected $attributes = [
		"name" => "EventRecur",
		"description" => "Appointment recurrence information",
		"model" => EventRecur::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"repeats" => [
				"type" => Type::string(),
				"description" =>
					"Hippo-Specific: How many intervals of time between recurrences",
				"alias" => "repeats",
			],
			"repeatsEvery" => [
				"type" => Type::string(),
				"description" =>
					"Hippo-Specific: String representation of recurrence pattern",
				"alias" => "repeats_every",
			],
			"repeatBy" => [
				"type" => Type::string(),
				"description" => "Hippo-Specific: Interval to repeat by",
				"alias" => "repeat_by",
			],
			"startDate" => [
				"type" => Type::string(),
				"description" => "The date the recurrence is calculated from",
				"alias" => "start_date",
			],
			"endType" => [
				"type" => Type::string(),
				"description" =>
					"Hippo-Specific: The nature of the termination of the recurrence",
				"alias" => "end_type",
			],
			"endDate" => [
				"type" => Type::string(),
				"description" =>
					"The date the recurrence should continue until",
				"alias" => "end_date",
			],
			"endOn" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "end_on",
			],
			"rrule" => [
				"type" => Type::string(),
				"description" =>
					"The google calendar style recurrence rule for the appointment recurrence pattern",
				"selectable" => false,
			],
			"skips" => (new EventRecurSkipField([
				"isList" => true,
				"description" => "Exceptions to this recurrence rule",
			]))->toArray(),
			"appointment" => (new AppointmentField([
				"description" =>
					"The appointment associated with the recurrence",
			]))->toArray(),
			"repeatsOn" => (new EventDaysField([
				"isList" => true,
				"description" =>
					"The days in which the associated appointment will occur",
			]))->toArray(),
		];
	}
}
