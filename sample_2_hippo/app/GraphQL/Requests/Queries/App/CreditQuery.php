<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\CreditArguments;
use App\GraphQL\Arguments\NameArguments;
use App\Models\Credit;

class CreditQuery extends AppHippoQuery
{
	protected $model = Credit::class;

	protected $permissionName = "Credits: Read";

	protected $attributes = [
		"name" => "creditQuery",
	];

	protected $arguments = [NameArguments::class, CreditArguments::class];
}
