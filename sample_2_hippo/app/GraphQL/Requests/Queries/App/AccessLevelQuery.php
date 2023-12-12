<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\AccessLevel;

class AccessLevelQuery extends AppHippoQuery
{
	protected $model = AccessLevel::class;

	protected $permissionName = "Roles: Read";

	protected $attributes = [
		"name" => "accessLevelQuery",
	];
}
