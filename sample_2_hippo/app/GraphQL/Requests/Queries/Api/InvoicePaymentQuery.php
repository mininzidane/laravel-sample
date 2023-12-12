<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\InvoicePayment;

class InvoicePaymentQuery extends ApiHippoQuery
{
	protected $model = InvoicePayment::class;

	protected $permissionName = "GraphQL: View Invoice Payments";

	protected $attributes = [
		"name" => "invoicePaymentQuery",
	];
}
