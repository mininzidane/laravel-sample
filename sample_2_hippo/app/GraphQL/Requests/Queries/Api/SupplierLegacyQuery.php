<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\SupplierLegacy;

class SupplierLegacyQuery extends ApiHippoQuery
{
	protected $model = SupplierLegacy::class;

	protected $permissionName = "GraphQL: View Legacy Suppliers";

	protected $attributes = [
		"name" => "supplierLegacyQuery",
	];
}
