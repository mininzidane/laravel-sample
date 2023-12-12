<?php

namespace App\Models;

use App\GraphQL\Types\LineItemGraphQLType;

class LineItem extends HippoModel
{
	public static $graphQLType = LineItemGraphQLType::class;

	protected $table = "ospos_sales_items";

	protected $fillable = [
		"sale_id",
		"item_id",
		"description",
		"serialnumber",
		"line",
		"quantity_purchased",
		"item_cost_price",
		"item_unit_price",
		"item_quantity_updated_chk",
		"discount_percent",
		"organization_id",
		"dispensing_fee",
		"receiving_item_id",
		"receiving_item_lot_number",
		"receiving_item_expiration_date",
		"receiving_item_line",
		"client_id",
		"seenby_id",
		"item_line_total",
		"item_line_discounted_total",
		"item_kit_id",
		"added_time",
		"updated_time",
	];

	public function sale()
	{
		return $this->belongsTo(Sale::class, "sale_id", "sale_id");
	}

	public function item()
	{
		return $this->belongsTo(ItemLegacy::class, "item_id", "item_id");
	}

	public function patient()
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function seenBy()
	{
		return $this->belongsTo(User::class, "seenby_id");
	}

	public function taxes()
	{
		return $this->hasMany(LineItemTax::class, "sale_item_id");
	}
}
