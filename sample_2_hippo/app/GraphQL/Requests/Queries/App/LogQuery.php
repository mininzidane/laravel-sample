<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\AccessLogArguments;
use App\Models\Log;

class LogQuery extends AppHippoQuery
{
	protected $model = Log::class;

	protected $permissionName = "Logs: Read";

	protected $attributes = [
		"name" => "logQuery",
	];

	protected $arguments = [AccessLogArguments::class];
}
