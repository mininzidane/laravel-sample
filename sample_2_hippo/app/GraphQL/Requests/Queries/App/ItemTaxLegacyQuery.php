<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemTaxLegacy;

class ItemTaxLegacyQuery extends AppHippoQuery
{
	protected $model = ItemTaxLegacy::class;

	protected $permissionName = "Legacy Item Taxes: Read";

	protected $attributes = [
		"name" => "itemTaxLegacyQuery",
	];
}
