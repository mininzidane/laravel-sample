<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\EventGraphQLType;

class EventField extends HippoField
{
	protected $graphQLType = EventGraphQLType::class;
	protected $permissionName = "GraphQL: View Events";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Events",
	];
}
