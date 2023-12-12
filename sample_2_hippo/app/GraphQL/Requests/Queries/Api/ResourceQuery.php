<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Resource;

class ResourceQuery extends ApiHippoQuery
{
	protected $model = Resource::class;

	protected $permissionName = "GraphQL: View Resources";

	protected $attributes = [
		"name" => "resourceQuery",
	];
}
