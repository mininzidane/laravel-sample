<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ReceivingItem;

class ReceivingItemQuery extends AppHippoQuery
{
	protected $model = ReceivingItem::class;

	protected $permissionName = "Receiving Items: Read";

	protected $attributes = [
		"name" => "receivingItemQuery",
	];
}
