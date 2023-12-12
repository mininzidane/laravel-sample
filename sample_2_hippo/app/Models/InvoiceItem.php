<?php

namespace App\Models;

use App\GraphQL\Types\InvoiceItemGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InvoiceItem
 *
 * @property-read int $id
 *
 * @property int $invoice_id
 * @property int $item_id
 * @property int $item_kit_id
 * @property int $provider_id
 * @property int $chart_id
 * @property string $chart_type
 * @property int $line
 * @property float $quantity
 * @property string $name
 * @property string $number
 * @property float $price
 * @property float $discount_percent
 * @property float $discount_amount
 * @property float $total
 * @property string $serial_number
 * @property string $administered_date
 * @property int $type_id
 * @property int $category_id
 * @property int $account_id
 * @property string $description
 * @property bool $allow_alt_description
 * @property float $cost_price
 * @property float $volume_price
 * @property float $volume_quantity
 * @property bool $apply_discount_to_remainder
 * @property float $markup_percentage
 * @property float $unit_price
 * @property float $minimum_sale_amount
 * @property float $dispensing_fee
 * @property bool $is_vaccine
 * @property bool $is_prescription
 * @property bool $is_serialized
 * @property bool $is_controlled_substance
 * @property bool $is_euthanasia
 * @property bool $is_reproductive
 * @property bool $hide_from_register
 * @property bool $requires_provider
 * @property bool $is_in_wellness_plan
 * @property int $vcp_item_id
 * @property string $drug_identifier
 * @property int $belongs_to_kit_id
 * @property bool $is_single_line_kit
 * @property int $receiving_item_id
 * @property int $credit_id
 * @property int $old_sale_item_id
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read bool $hasInventory
 *
 * @property-read Invoice $invoice
 * @property-read Collection|InventoryTransaction[] $inventoryTransactions
 * @property-read Item $item
 * @property-read ItemType $itemType
 * @property-read ItemCategory $itemCategory
 * @property-read ChartOfAccounts $chartOfAccount
 * @property-read ReminderInterval $reminderInterval
 * @property-read Collection|InvoiceItemTax[] $invoiceItemTaxes
 * @property-read User $provider
 * @property-read Collection|Reminder[] $reminders
 * @property-read Collection|InvoiceAppliedDiscount[] $appliedDiscounts
 * @property-read Collection|InvoiceAppliedDiscount[] $discountApplications
 * @property-read Vaccination $vaccination
 * @property-read Credit $credit
 * @property-read Item $itemKit
 */
class InvoiceItem extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = InvoiceItemGraphQLType::class;

	protected $table = "invoice_items";

	protected $fillable = [
		"quantity",
		"name",
		"number",
		"price",
		"discount_percent",
		"discount_amount",
		"total",
		"serial_number",
		"administered_date",
		"description",
		"allow_alt_description",
		"cost_price",
		"volume_price",
		"volume_quantity",
		"apply_discount_to_remainder",
		"markup_percentage",
		"unit_price",
		"minimum_sale_amount",
		"dispensing_fee",
		"is_vaccine",
		"is_prescription",
		"is_serialized",
		"is_controlled_substance",
		"is_euthanasia",
		"is_reproductive",
		"hide_from_register",
		"requires_provider",
		"is_in_wellness_plan",
		"vcp_item_id",
		"drug_identifier",
		"belongs_to_kit_id",
		"is_single_line_kit",
		"credit_id",
		"item_id",
		"item_kit_id",
		"chartOfAccount",
	];

	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

	public function inventoryTransactions(): HasMany
	{
		return $this->hasMany(InventoryTransaction::class);
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function itemType(): BelongsTo
	{
		return $this->belongsTo(ItemType::class, "type_id", "id");
	}

	public function itemCategory(): BelongsTo
	{
		return $this->belongsTo(ItemCategory::class, "category_id", "id");
	}

	public function chartOfAccount(): BelongsTo
	{
		return $this->belongsTo(ChartOfAccounts::class, "account_id", "id");
	}

	public function reminderInterval(): BelongsTo
	{
		return $this->belongsTo(ReminderInterval::class);
	}

	public function invoiceItemTaxes(): HasMany
	{
		return $this->hasMany(InvoiceItemTax::class);
	}

	public function provider(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function chart(): MorphTo
	{
		return $this->morphTo();
	}

	public function reminders(): HasMany
	{
		return $this->hasMany(Reminder::class, "invoice_item_id");
	}

	public function appliedDiscounts(): HasMany
	{
		return $this->hasMany(
			InvoiceAppliedDiscount::class,
			"discount_invoice_item_id",
		);
	}

	public function discountApplications(): HasMany
	{
		return $this->hasMany(
			InvoiceAppliedDiscount::class,
			"adjusted_invoice_item_id",
		);
	}

	public function vaccination(): HasOne
	{
		return $this->hasOne(Vaccination::class);
	}

	public function credit(): BelongsTo
	{
		return $this->belongsTo(Credit::class);
	}

	public function itemKit(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function getHasInventoryAttribute(): bool
	{
		return $this->itemType->process_inventory;
	}
}
