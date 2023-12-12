<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\NameArguments;
use App\Models\Species;

class SpeciesQuery extends ApiHippoQuery
{
	protected $model = Species::class;

	protected $permissionName = "GraphQL: View Species";

	protected $attributes = [
		"name" => "speciesQuery",
	];

	protected $arguments = [NameArguments::class];
}
