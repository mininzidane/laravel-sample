<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Resource;

class ResourceQuery extends AppHippoQuery
{
	protected $model = Resource::class;

	protected $permissionName = "Resources: Read";

	protected $attributes = [
		"name" => "resourceQuery",
	];
}
