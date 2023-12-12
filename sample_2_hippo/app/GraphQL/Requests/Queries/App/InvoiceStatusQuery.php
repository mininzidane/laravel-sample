<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\InvoiceStatus;

class InvoiceStatusQuery extends AppHippoQuery
{
	protected $model = InvoiceStatus::class;

	protected $permissionName = "Invoice Statuses: Read";

	protected $attributes = [
		"name" => "invoiceStatusQuery",
	];
}
