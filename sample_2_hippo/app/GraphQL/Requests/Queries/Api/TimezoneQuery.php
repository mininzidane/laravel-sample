<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Timezone;

class TimezoneQuery extends ApiHippoQuery
{
	protected $model = Timezone::class;

	protected $permissionName = "GraphQL: View Timezones";

	protected $attributes = [
		"name" => "timezoneQuery",
	];
}
