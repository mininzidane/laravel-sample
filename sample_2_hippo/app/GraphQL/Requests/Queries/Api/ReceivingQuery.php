<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\ReceivingArguments;
use App\Models\Receiving;

class ReceivingQuery extends ApiHippoQuery
{
	protected $model = Receiving::class;

	protected $permissionName = "GraphQL: View Receivings";

	protected $attributes = [
		"name" => "receivingQuery",
	];

	protected $arguments = [ReceivingArguments::class];
}
