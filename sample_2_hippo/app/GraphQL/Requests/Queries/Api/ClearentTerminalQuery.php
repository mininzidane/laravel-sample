<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\LocationsArguments;
use App\GraphQL\Arguments\NameArguments;
use App\Models\ClearentTerminal;

class ClearentTerminalQuery extends ApiHippoQuery
{
	protected $model = ClearentTerminal::class;

	protected $permissionName = "GraphQL: View Clearent Terminals";

	protected $attributes = [
		"name" => "clearentTerminalQuery",
	];

	protected $arguments = [NameArguments::class, LocationsArguments::class];
}
