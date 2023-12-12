<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\EventRecurGraphQLType;

class EventRecurField extends HippoField
{
	protected $graphQLType = EventRecurGraphQLType::class;
	protected $permissionName = "GraphQL: View Event Recurrences";
	protected $isList = false;

	// Must include additional columns for the calculation of the rrule
	protected $attributes = [
		"description" => "Associated Event Recurrences",
		"always" => [
			"repeats",
			"repeatsEvery",
			"repeatBy",
			"startDate",
			"endDate",
			"endOn",
		],
	];
}
