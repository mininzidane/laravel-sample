<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\StateArguments;
use App\Models\State;

class StateQuery extends ApiHippoQuery
{
	protected $model = State::class;

	protected $permissionName = "GraphQL: View States";

	protected $attributes = [
		"name" => "stateQuery",
	];

	protected $arguments = [StateArguments::class];
}
