<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\InvoiceItem;

class InvoiceItemQuery extends ApiHippoQuery
{
	protected $model = InvoiceItem::class;

	protected $permissionName = "GraphQL: View Invoice Items";

	protected $attributes = [
		"name" => "invoiceItemQuery",
	];
}
