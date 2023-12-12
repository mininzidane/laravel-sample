<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\LocationGraphQLType;

class LocationField extends HippoField
{
	protected $graphQLType = LocationGraphQLType::class;
	protected $permissionName = "GraphQL: View Locations";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Locations",
	];
}
