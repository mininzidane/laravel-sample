<?php

namespace App\Models;

use App\GraphQL\Types\ItemLegacyGraphQLType;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin \Eloquent
 */
class ItemLegacy extends HippoModel
{
	use HasName;
	use SoftDeletes;

	public static $graphQLType = ItemLegacyGraphQLType::class;

	protected $table = "ospos_items";

	protected $primaryKey = "item_id";

	protected $fillable = [
		"name",
		"category",
		"supplier_id",
		"item_number",
		"description",
		"cost_price",
		"unit_price",
		"quantity",
		"reorder_level",
		"location",
		"item_id",
		"allow_alt_description",
		"is_serialized",
		"deleted",
		"organization_id",
		"procedure",
		"location_id",
		"vaccine",
		"lot_number",
		"rabies_tag",
		"concept_id",
		"has_children",
		"prescription",
		"dispensing_fee",
		"controlled_substance",
		"non_stocking",
		"expiration_date",
		"discount_code",
		"reminder",
		"euthanasia",
		"reproductive",
		"cost_percentage",
		"labtest",
		"giftcard",
		"account_credit",
		"hide_from_sales",
		"min_sale_amount",
		"discount_qtys",
		"discount_qtys_price",
		"discount_remainder",
		"receiving_id",
		"receiving_line",
	];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name"];

		parent::__construct($attributes);
	}

	public function location()
	{
		return $this->belongsTo(Location::class, "location_id");
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function lineItems()
	{
		return $this->hasMany(LineItem::class, "item_id", "item_id");
	}

	public function sales()
	{
		return $this->belongsToMany(
			Sale::class,
			"ospos_sales_items",
			"item_id",
			"sale_id",
		)->using(LineItemPivot::class);
	}

	public function reminders()
	{
		return $this->hasMany(Reminder::class, "item_id");
	}

	public function itemTaxes()
	{
		return $this->hasMany(ItemTaxLegacy::class, "item_id");
	}

	public function receivingItems()
	{
		return $this->hasMany(ReceivingItemLegacy::class, "item_id");
	}

	public function supplier()
	{
		return $this->belongsTo(SupplierLegacy::class, "supplier_id");
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class, "item_id");
	}

	public function vaccinations()
	{
		return $this->hasMany(Vaccination::class, "vaccine_item_id");
	}

	public function newItem()
	{
		return $this->hasOne(Item::class);
	}
}
