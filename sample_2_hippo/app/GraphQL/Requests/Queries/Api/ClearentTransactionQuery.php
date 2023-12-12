<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\ClearentTransactionArguments;
use App\Models\ClearentTransaction;

class ClearentTransactionQuery extends ApiHippoQuery
{
	protected $model = ClearentTransaction::class;

	protected $permissionName = "GraphQL: View Clearent Transactions";

	protected $attributes = [
		"name" => "clearentTransactionQuery",
	];

	protected $arguments = [ClearentTransactionArguments::class];
}
