<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\LocationsArguments;
use App\Models\Location;

class LocationQuery extends AppHippoQuery
{
	protected $model = Location::class;

	protected $permissionName = "Locations: Read";

	protected $attributes = [
		"name" => "locationQuery",
	];

	protected $arguments = [LocationsArguments::class];
}
