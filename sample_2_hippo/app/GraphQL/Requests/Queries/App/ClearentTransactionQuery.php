<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\ClearentTransactionArguments;
use App\Models\ClearentTransaction;

class ClearentTransactionQuery extends AppHippoQuery
{
	protected $model = ClearentTransaction::class;

	protected $permissionName = "Clearent Transactions: Read";

	protected $attributes = [
		"name" => "clearentTransactionQuery",
	];

	protected $arguments = [ClearentTransactionArguments::class];
}
