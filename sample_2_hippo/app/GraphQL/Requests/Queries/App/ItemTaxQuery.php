<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemTax;

class ItemTaxQuery extends AppHippoQuery
{
	protected $model = ItemTax::class;

	protected $permissionName = "Item Taxes: Read";

	protected $attributes = [
		"name" => "itemTaxQuery",
	];
}
