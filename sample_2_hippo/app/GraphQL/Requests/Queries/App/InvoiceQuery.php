<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\InvoiceArguments;
use App\GraphQL\Arguments\PatientArguments;
use App\Models\Invoice;

class InvoiceQuery extends AppHippoQuery
{
	protected $model = Invoice::class;

	protected $permissionName = "Invoices: Read";

	protected $attributes = [
		"name" => "invoiceQuery",
	];

	protected $arguments = [InvoiceArguments::class];
}
