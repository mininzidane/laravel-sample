<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ReceivingLegacy;

class ReceivingLegacyQuery extends ApiHippoQuery
{
	protected $model = ReceivingLegacy::class;

	protected $permissionName = "GraphQL: View Legacy Receivings";

	protected $attributes = [
		"name" => "receivingLegacyQuery",
	];
}
