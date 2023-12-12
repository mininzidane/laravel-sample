<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\NameArguments;
use App\Models\Gender;

class GenderQuery extends ApiHippoQuery
{
	protected $model = Gender::class;

	protected $permissionName = "GraphQL: View Genders";

	protected $attributes = [
		"name" => "genderQuery",
	];

	protected $arguments = [NameArguments::class];
}
