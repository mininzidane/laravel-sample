<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\MucousMembraneStatus;
use App\GraphQL\Arguments\NameArguments;

class MucousMembraneStatusQuery extends AppHippoQuery
{
	protected $model = MucousMembraneStatus::class;

	protected $permissionName = "Mucous Membrane Statuses: Read";

	protected $attributes = [
		"name" => "mucousMembraneStatusQuery",
	];

	protected $arguments = [NameArguments::class];
}
