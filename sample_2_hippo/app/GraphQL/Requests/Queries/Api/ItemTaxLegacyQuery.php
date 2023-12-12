<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemTaxLegacy;

class ItemTaxLegacyQuery extends ApiHippoQuery
{
	protected $model = ItemTaxLegacy::class;

	protected $permissionName = "GraphQL: View Legacy Item Taxes";

	protected $attributes = [
		"name" => "itemTaxLegacyQuery",
	];
}
