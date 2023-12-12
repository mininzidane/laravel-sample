<?php

namespace App\Models;

use App\GraphQL\Types\ItemGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Item
 *
 * @property int $id
 * @property int $type_id
 * @property string $name
 * @property string $number
 * @property int $category_id
 * @property int $account_id
 * @property string $description
 * @property bool $allow_alt_description
 * @property float $cost_price
 * @property float $markup_percentage
 * @property float $unit_price
 * @property float $minimum_sale_amount
 * @property float $dispensing_fee
 * @property bool $apply_discount_to_remainder
 * @property bool $is_non_taxable
 * @property bool $is_vaccine
 * @property bool $is_prescription
 * @property bool $is_serialized
 * @property bool $is_controlled_substance
 * @property bool $is_euthanasia
 * @property bool $is_reproductive
 * @property bool $hide_from_register
 * @property bool $requires_provider
 * @property bool $is_in_wellness_plan
 * @property int $reminder_interval_id
 * @property float $minimum_on_hand
 * @property float $maximum_on_hand
 * @property int $vcp_item_id
 * @property string $next_tag_number
 * @property string $drug_identifier
 * @property bool $is_single_line_kit
 * @property int $old_item_id
 * @property int $old_item_kit_id
 * @property ItemCategory $category
 * @property ItemType $itemType
 * @property Supplier $manufacturer
 */
class Item extends HippoModel
{
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = ItemGraphQLType::class;

	protected $table = "items";

	protected $fillable = [
		"type_id",
		"name",
		"number",
		"category_id",
		"account_id",
		"description",
		"allow_alt_description",
		"cost_price",
		"markup_percentage",
		"unit_price",
		"minimum_sale_amount",
		"dispensing_fee",
		"apply_discount_to_remainder",
		"is_non_taxable",
		"is_vaccine",
		"is_prescription",
		"is_serialized",
		"is_controlled_substance",
		"is_euthanasia",
		"is_reproductive",
		"hide_from_register",
		"requires_provider",
		"is_in_wellness_plan",
		"reminder_interval_id",
		"minimum_on_hand",
		"maximum_on_hand",
		"vcp_item_id",
		"next_tag_number",
		"drug_identifier",
		"is_single_line_kit",
		"old_item_id",
		"old_item_kit_id",
		"manufacturer_id",
	];

	protected $appends = ["remaining", "categoryName"];

	public function itemType()
	{
		return $this->belongsTo(ItemType::class, "type_id", "id");
	}

	public function itemCategory()
	{
		return $this->belongsTo(ItemCategory::class, "category_id", "id");
	}

	public function category()
	{
		return $this->belongsTo(ItemCategory::class, "category_id");
	}

	public function chartOfAccount()
	{
		return $this->belongsTo(ChartOfAccounts::class, "account_id", "id");
	}

	public function reminderInterval()
	{
		return $this->belongsTo(ReminderInterval::class);
	}

	public function taxes()
	{
		return $this->belongsToMany(Tax::class, "item_taxes");
	}

	public function itemTaxes()
	{
		return $this->hasMany(ItemTax::class);
	}

	public function itemLocations()
	{
		return $this->hasMany(ItemLocation::class);
	}

	public function itemVolumePricing()
	{
		return $this->hasMany(ItemVolumePricing::class);
	}

	public function itemSpeciesRestrictions()
	{
		return $this->hasMany(ItemSpeciesRestriction::class);
	}

	public function receivings()
	{
		return $this->belongsToMany(
			Receiving::class,
			"receiving_items",
			"item_id",
			"receiving_id",
		);
	}

	public function inventory()
	{
		return $this->hasMany(Inventory::class);
	}

	public function invoiceItems()
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function reminderReplaces()
	{
		return $this->hasMany(ItemReminderReplaces::class, "item_id");
	}

	public function replacedItems()
	{
		return $this->hasMany(ItemReplaces::class);
	}

	public function itemKits()
	{
		return $this->hasMany(ItemKitItem::class, "item_id");
	}

	public function itemKitItems()
	{
		return $this->hasMany(ItemKitItem::class, "item_kit_id");
	}

	public function itemLegacy()
	{
		return $this->belongsTo(ItemLegacy::class);
	}

	public function itemKitLegacy()
	{
		// Consideration: Define model for item kit legacy and configure relation
	}

	public function manufacturer()
	{
		return $this->belongsTo(Supplier::class, "manufacturer_id", "id");
	}

	public function reminderIntervals()
	{
		return $this->hasMany(ItemReminderInterval::class, "item_id");
	}

	public function getRemainingAttribute()
	{
		$connection = $this->getConnectionName();

		$completeStatus = InventoryStatus::on($connection)
			->where("name", "Complete")
			->firstOrFail();

		return Inventory::on($connection)
			->where("status_id", $completeStatus->id)
			->where("item_id", $this->id)
			->sum("remaining_quantity");
	}

	public function getLocationQuantityAttribute($root, $location)
	{
		$connection = $this->getConnectionName();
		$item = $root->id;

		$quantity = Inventory::on($connection)
			->where("item_id", $item)
			->when($location, function ($query, $location) {
				return $query->where("location_id", $location);
			})
			->sum("remaining_quantity");

		return $quantity ?? 0;
	}

	public function treatmentSheetTreatments()
	{
		return $this->hasMany(TreatmentSheetTreatment::class, "item_id");
	}

	public function getCategoryNameAttribute()
	{
		return $this->category->name ?? null;
	}

	public function getHasInventoryAttribute()
	{
		return $this->itemType->process_inventory;
	}
}
