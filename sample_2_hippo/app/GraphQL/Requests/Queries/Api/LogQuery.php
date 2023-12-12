<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Log;

class LogQuery extends ApiHippoQuery
{
	protected $model = Log::class;

	protected $permissionName = "GraphQL: View Logs";

	protected $attributes = [
		"name" => "itemQuery",
	];

	protected $arguments = [];
}
