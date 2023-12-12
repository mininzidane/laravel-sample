<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\StateArguments;
use App\Models\State;

class StateQuery extends AppHippoQuery
{
	protected $model = State::class;

	protected $permissionName = "States: Read";

	protected $attributes = [
		"name" => "stateQuery",
	];

	protected $arguments = [StateArguments::class];
}
