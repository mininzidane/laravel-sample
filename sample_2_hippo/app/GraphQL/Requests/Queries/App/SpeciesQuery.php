<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\NameArguments;
use App\Models\Species;

class SpeciesQuery extends AppHippoQuery
{
	protected $model = Species::class;

	protected $permissionName = "Species: Read";

	protected $attributes = [
		"name" => "speciesQuery",
	];

	protected $arguments = [NameArguments::class];
}
