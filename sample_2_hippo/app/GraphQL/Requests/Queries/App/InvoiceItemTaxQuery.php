<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\InvoiceItemTax;

class InvoiceItemTaxQuery extends AppHippoQuery
{
	protected $model = InvoiceItemTax::class;

	protected $permissionName = "Invoice Item Taxes: Read";

	protected $attributes = [
		"name" => "invoiceItemTaxQuery",
	];
}
