<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\SaleStatus;

class SaleStatusQuery extends ApiHippoQuery
{
	protected $model = SaleStatus::class;

	protected $permissionName = "GraphQL: View Sale Statuses";

	protected $attributes = [
		"name" => "saleStatusQuery",
	];
}
