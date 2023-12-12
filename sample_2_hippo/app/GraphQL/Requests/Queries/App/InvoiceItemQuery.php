<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\InvoiceItemArguments;
use App\Models\InvoiceItem;

class InvoiceItemQuery extends AppHippoQuery
{
	protected $model = InvoiceItem::class;

	protected $permissionName = "Invoice Items: Read";

	protected $attributes = [
		"name" => "invoiceItemQuery",
	];

	protected $arguments = [InvoiceItemArguments::class];
}
