<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemVolumePricing;

class ItemVolumePricingQuery extends ApiHippoQuery
{
	protected $model = ItemVolumePricing::class;

	protected $permissionName = "GraphQL: View Item Volume Pricing";

	protected $attributes = [
		"name" => "itemVolumePricingQuery",
	];
}
