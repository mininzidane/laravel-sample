<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\CreditArguments;
use App\GraphQL\Arguments\NameArguments;
use App\Models\Credit;

class CreditQuery extends ApiHippoQuery
{
	protected $model = Credit::class;

	protected $permissionName = "GraphQL: View Credits";

	protected $attributes = [
		"name" => "creditQuery",
	];

	protected $arguments = [NameArguments::class, CreditArguments::class];
}
