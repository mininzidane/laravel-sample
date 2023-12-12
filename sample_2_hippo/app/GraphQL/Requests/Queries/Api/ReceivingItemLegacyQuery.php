<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ReceivingItemLegacy;

class ReceivingItemLegacyQuery extends ApiHippoQuery
{
	protected $model = ReceivingItemLegacy::class;

	protected $permissionName = "GraphQL: View Legacy Receiving Items";

	protected $attributes = [
		"name" => "receivingLegacyItemQuery",
	];
}
