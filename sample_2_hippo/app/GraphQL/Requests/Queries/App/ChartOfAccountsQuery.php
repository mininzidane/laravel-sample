<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ChartOfAccounts;

class ChartOfAccountsQuery extends AppHippoQuery
{
	protected $model = ChartOfAccounts::class;

	protected $permissionName = "Accounts: Read";

	protected $attributes = [
		"name" => "chartOfAccountsQuery",
	];
}
