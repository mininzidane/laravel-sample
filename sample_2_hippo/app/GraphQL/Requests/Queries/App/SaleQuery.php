<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\DatedArguments;
use App\GraphQL\Arguments\SaleStatusArguments;
use App\GraphQL\Arguments\TimestampArguments;
use App\Models\Sale;

class SaleQuery extends AppHippoQuery
{
	protected $model = Sale::class;

	protected $permissionName = "Legacy Sales: Read";

	protected $attributes = [
		"name" => "saleQuery",
	];

	protected $arguments = [
		TimestampArguments::class,
		DatedArguments::class,
		SaleStatusArguments::class,
	];
}
