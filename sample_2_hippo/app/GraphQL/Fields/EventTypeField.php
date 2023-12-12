<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\EventTypeGraphQLType;

class EventTypeField extends HippoField
{
	protected $graphQLType = EventTypeGraphQLType::class;
	protected $permissionName = "GraphQL: View Event Types";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Event Types",
	];
}
