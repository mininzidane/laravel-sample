<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\InvoiceItemTax;

class InvoiceItemTaxQuery extends ApiHippoQuery
{
	protected $model = InvoiceItemTax::class;

	protected $permissionName = "GraphQL: View Invoice Item Taxes";

	protected $attributes = [
		"name" => "invoiceItemTaxQuery",
	];
}
