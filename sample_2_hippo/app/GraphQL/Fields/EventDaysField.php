<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\EventDaysGraphQLType;
use App\GraphQL\Types\EventRecurSkipGraphQLType;

class EventDaysField extends HippoField
{
	protected $graphQLType = EventDaysGraphQLType::class;
	protected $permissionName = "GraphQL: View Event Recurrence Skips";
	protected $isList = true;

	// Must include additional columns for the calculation of the rrule
	protected $attributes = [
		"description" => "Associated Event Recurrence Days",
		"always" => ["day"],
	];
}
