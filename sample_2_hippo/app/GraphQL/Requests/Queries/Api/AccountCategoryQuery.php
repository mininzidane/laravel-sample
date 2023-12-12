<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\AccountCategory;

class AccountCategoryQuery extends ApiHippoQuery
{
	protected $model = AccountCategory::class;

	protected $permissionName = "GraphQL: View Account Categories";

	protected $attributes = [
		"name" => "accountCategoryQuery",
	];
}
