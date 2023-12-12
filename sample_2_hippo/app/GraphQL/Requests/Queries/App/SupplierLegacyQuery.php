<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\SupplierLegacy;

class SupplierLegacyQuery extends AppHippoQuery
{
	protected $model = SupplierLegacy::class;

	protected $permissionName = "Legacy Suppliers: Read";

	protected $attributes = [
		"name" => "supplierLegacyQuery",
	];
}
