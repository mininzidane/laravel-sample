<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\LocationsArguments;
use App\GraphQL\Arguments\NameArguments;
use App\Models\ClearentTerminal;

class ClearentTerminalQuery extends AppHippoQuery
{
	protected $model = ClearentTerminal::class;

	protected $permissionName = "Clearent Terminals: Read";

	protected $attributes = [
		"name" => "clearentTerminalQuery",
	];

	protected $arguments = [NameArguments::class, LocationsArguments::class];
}
