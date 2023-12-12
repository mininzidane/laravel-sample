<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ReceivingItem;

class ReceivingItemQuery extends ApiHippoQuery
{
	protected $model = ReceivingItem::class;

	protected $permissionName = "GraphQL: View Receiving Items";

	protected $attributes = [
		"name" => "receivingItemQuery",
	];
}
