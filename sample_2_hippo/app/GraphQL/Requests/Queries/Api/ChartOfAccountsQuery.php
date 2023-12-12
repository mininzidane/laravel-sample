<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ChartOfAccounts;

class ChartOfAccountsQuery extends ApiHippoQuery
{
	protected $model = ChartOfAccounts::class;

	protected $permissionName = "GraphQL: View Chart of Accounts";

	protected $attributes = [
		"name" => "chartOfAccountsQuery",
	];
}
