<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Location;

class LocationQuery extends ApiHippoQuery
{
	protected $model = Location::class;

	protected $permissionName = "GraphQL: View Locations";

	protected $attributes = [
		"name" => "locationQuery",
	];
}
