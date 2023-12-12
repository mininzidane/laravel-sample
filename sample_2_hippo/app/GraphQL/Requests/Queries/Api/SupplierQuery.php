<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Supplier;

class SupplierQuery extends ApiHippoQuery
{
	protected $model = Supplier::class;

	protected $permissionName = "GraphQL: View Suppliers";

	protected $attributes = [
		"name" => "supplierQuery",
	];
}
