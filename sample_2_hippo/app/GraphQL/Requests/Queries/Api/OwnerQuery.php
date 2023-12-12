<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Owner;

class OwnerQuery extends ApiHippoQuery
{
	protected $model = Owner::class;

	protected $permissionName = "GraphQL: View Owners";

	protected $attributes = [
		"name" => "ownerQuery",
	];
}
