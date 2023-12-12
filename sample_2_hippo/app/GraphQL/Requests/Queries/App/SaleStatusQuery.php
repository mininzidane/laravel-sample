<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\SaleStatus;

class SaleStatusQuery extends AppHippoQuery
{
	protected $model = SaleStatus::class;

	protected $permissionName = "Legacy Sale Statuses: Read";

	protected $attributes = [
		"name" => "saleStatusQuery",
	];
}
