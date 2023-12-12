<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Gender;
use App\GraphQL\Arguments\NameArguments;
use App\GraphQL\Arguments\GenderArguments;

class GenderQuery extends AppHippoQuery
{
	protected $model = Gender::class;

	protected $permissionName = "Genders: Read";

	protected $attributes = [
		"name" => "genderQuery",
	];

	protected $arguments = [NameArguments::class, GenderArguments::class];
}
