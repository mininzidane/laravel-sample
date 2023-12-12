<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ReceivingStatus;

class ReceivingStatusQuery extends AppHippoQuery
{
	protected $model = ReceivingStatus::class;

	protected $permissionName = "Receiving Statuses: Read";

	protected $attributes = [
		"name" => "receivingStatusQuery",
	];
}
