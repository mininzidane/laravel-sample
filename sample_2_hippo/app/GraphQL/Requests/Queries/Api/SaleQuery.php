<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\DatedArguments;
use App\GraphQL\Arguments\SaleStatusArguments;
use App\GraphQL\Arguments\TimestampArguments;
use App\Models\Sale;

class SaleQuery extends ApiHippoQuery
{
	protected $model = Sale::class;

	protected $permissionName = "GraphQL: View Sales";

	protected $attributes = [
		"name" => "saleQuery",
	];

	protected $arguments = [
		TimestampArguments::class,
		DatedArguments::class,
		SaleStatusArguments::class,
	];
}
