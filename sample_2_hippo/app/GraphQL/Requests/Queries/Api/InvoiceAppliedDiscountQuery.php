<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\InvoiceAppliedDiscountArguments;
use App\Models\InvoiceAppliedDiscount;

class InvoiceAppliedDiscountQuery extends ApiHippoQuery
{
	protected $model = InvoiceAppliedDiscount::class;

	protected $permissionName = "GraphQL: View Invoice Applied Discounts";

	protected $attributes = [
		"name" => "invoiceAppliedDiscountQuery",
	];

	protected $arguments = [InvoiceAppliedDiscountArguments::class];
}
