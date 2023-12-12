<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\EventRecurSkipGraphQLType;

class EventRecurSkipField extends HippoField
{
	protected $graphQLType = EventRecurSkipGraphQLType::class;
	protected $permissionName = "GraphQL: View Event Recurrence Skips";
	protected $isList = false;

	// Must include additional columns for the calculation of the rrule
	protected $attributes = [
		"description" => "Associated Event Recurrence Skips",
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
