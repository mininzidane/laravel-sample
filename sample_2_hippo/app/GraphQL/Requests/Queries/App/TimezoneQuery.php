<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Timezone;

class TimezoneQuery extends AppHippoQuery
{
	protected $model = Timezone::class;

	protected $permissionName = "Timezones: Read";

	protected $attributes = [
		"name" => "timezoneQuery",
	];
}
