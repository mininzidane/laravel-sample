<?php

namespace App\Models;

use App\GraphQL\Types\LineItemTaxGraphQLType;

class LineItemTax extends HippoModel
{
	public static $graphQLType = LineItemTaxGraphQLType::class;

	protected $table = "vw_ospos_sales_item_taxes";

	public function item()
	{
		return $this->belongsTo(ItemLegacy::class, "item_id");
	}

	public function sale()
	{
		return $this->belongsTo(Sale::class, "sale_id");
	}
}
