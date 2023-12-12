<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\NameArguments;
use App\Models\InvoiceStatus;

class InvoiceStatusQuery extends ApiHippoQuery
{
	protected $model = InvoiceStatus::class;

	protected $permissionName = "GraphQL: View Invoice Statuses";

	protected $attributes = [
		"name" => "invoiceStatusQuery",
	];

	protected $arguments = [NameArguments::class];
}
