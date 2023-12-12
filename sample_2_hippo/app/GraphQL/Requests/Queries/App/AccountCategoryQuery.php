<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\AccountCategory;

class AccountCategoryQuery extends AppHippoQuery
{
	protected $model = AccountCategory::class;

	protected $permissionName = "Account Categories: Read";

	protected $attributes = [
		"name" => "accountCategoryQuery",
	];
}
