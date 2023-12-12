<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ReceivingLegacy;

class ReceivingLegacyQuery extends AppHippoQuery
{
	protected $model = ReceivingLegacy::class;

	protected $permissionName = "Legacy Receivings: Read";

	protected $attributes = [
		"name" => "receivingLegacyQuery",
	];
}
