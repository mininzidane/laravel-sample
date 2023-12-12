<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\LogActionArguments;
use App\Models\LogAction;

class LogActionQuery extends AppHippoQuery
{
	protected $model = LogAction::class;

	protected $permissionName = "Log Actions: Read";

	protected $attributes = [
		"name" => "logActionQuery",
	];

	protected $arguments = [LogActionArguments::class];
}
