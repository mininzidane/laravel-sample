<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\InvoicePayment;

class InvoicePaymentQuery extends AppHippoQuery
{
	protected $model = InvoicePayment::class;

	protected $permissionName = "Invoice Payments: Read";

	protected $attributes = [
		"name" => "invoicePaymentQuery",
	];
}
