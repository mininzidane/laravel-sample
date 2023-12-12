<?php

namespace App\Models;

use App\GraphQL\Types\ReceivingItemLegacyGraphQLType;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivingItemLegacy extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;

	public static $graphQLType = ReceivingItemLegacyGraphQLType::class;

	protected $table = "ospos_receivings_items";

	protected $fillable = [
		"receiving_id",
		"item_id",
		"description",
		"serialnumber",
		"line",
		"quantity_purchased",
		"current_quantity",
		"lot_number",
		"expiration_date",
		"use_for_inventory",
		"item_cost_price",
		"item_unit_price",
		"discount_percent",
		"organization_id",
	];

	public function receiving()
	{
		return $this->belongsTo(ReceivingLegacy::class, "receiving_id");
	}

	public function item()
	{
		return $this->belongsTo(ItemLegacy::class, "item_id");
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}
}
