<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\ReceivingArguments;
use App\Models\Receiving;

class ReceivingQuery extends AppHippoQuery
{
	protected $model = Receiving::class;

	protected $permissionName = "Receivings: Read";

	protected $attributes = [
		"name" => "receivingQuery",
	];

	protected $arguments = [ReceivingArguments::class];
}
