<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemTax;

class ItemTaxQuery extends ApiHippoQuery
{
	protected $model = ItemTax::class;

	protected $permissionName = "GraphQL: View Item Taxes";

	protected $attributes = [
		"name" => "itemTaxQuery",
	];
}
