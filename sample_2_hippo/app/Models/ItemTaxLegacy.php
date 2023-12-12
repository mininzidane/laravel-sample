<?php

namespace App\Models;

use App\GraphQL\Types\ItemTaxLegacyGraphQLType;

class ItemTaxLegacy extends HippoModel
{
	public static $graphQLType = ItemTaxLegacyGraphQLType::class;

	protected $table = "vw_ospos_sales_item_taxes";

	public function item()
	{
		return $this->hasMany(ItemLegacy::class, "item_id");
	}
}
