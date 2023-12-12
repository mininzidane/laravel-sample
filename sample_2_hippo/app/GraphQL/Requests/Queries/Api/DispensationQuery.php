<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Dispensation;

class DispensationQuery extends ApiHippoQuery
{
	protected $model = Dispensation::class;

	protected $permissionName = "GraphQL: View Dispensations";

	protected $attributes = [
		"name" => "dispensationQuery",
	];
}
