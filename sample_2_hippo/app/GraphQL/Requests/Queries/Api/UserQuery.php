<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\UserArguments;
use App\Models\User;

class UserQuery extends ApiHippoQuery
{
	protected $model = User::class;

	protected $permissionName = "GraphQL: View Users";

	protected $attributes = [
		"name" => "userQuery",
	];

	protected $arguments = [UserArguments::class];
}
