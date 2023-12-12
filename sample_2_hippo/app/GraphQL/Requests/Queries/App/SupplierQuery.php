<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Supplier;

class SupplierQuery extends AppHippoQuery
{
	protected $model = Supplier::class;

	protected $permissionName = "Suppliers: Read";

	protected $attributes = [
		"name" => "supplierQuery",
	];
}
