<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ReceivingItemLegacy;

class ReceivingItemLegacyQuery extends AppHippoQuery
{
	protected $model = ReceivingItemLegacy::class;

	protected $permissionName = "Legacy Receiving Items: Read";

	protected $attributes = [
		"name" => "receivingLegacyItemQuery",
	];
}
