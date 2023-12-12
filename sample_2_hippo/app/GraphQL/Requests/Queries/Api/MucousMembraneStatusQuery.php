<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\MucousMembraneStatus;

class MucousMembraneStatusQuery extends ApiHippoQuery
{
	protected $model = MucousMembraneStatus::class;

	protected $permissionName = "GraphQL: View Mucous Membrane Statuses";

	protected $attributes = [
		"name" => "mucousMembraneStatusQuery",
	];
}
