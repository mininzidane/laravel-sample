<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemVolumePricing;

class ItemVolumePricingQuery extends AppHippoQuery
{
	protected $model = ItemVolumePricing::class;

	protected $permissionName = "Item Volume Pricing: Read";

	protected $attributes = [
		"name" => "itemVolumePricingQuery",
	];
}
