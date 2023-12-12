<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\InvoiceArguments;
use App\GraphQL\Arguments\PatientArguments;
use App\Models\Invoice;

class InvoiceQuery extends ApiHippoQuery
{
	protected $model = Invoice::class;

	protected $permissionName = "GraphQL: View Invoices";

	protected $attributes = [
		"name" => "invoiceQuery",
	];

	protected $arguments = [InvoiceArguments::class];
}
