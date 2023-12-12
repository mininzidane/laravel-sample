<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\NameArguments;
use App\Models\ReceivingStatus;

class ReceivingStatusQuery extends ApiHippoQuery
{
	protected $model = ReceivingStatus::class;

	protected $permissionName = "GraphQL: View Receiving Statuses";

	protected $attributes = [
		"name" => "receivingStatusQuery",
	];

	protected $arguments = [NameArguments::class];
}
