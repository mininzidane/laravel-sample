<?php

namespace App\Models;

use App\GraphQL\Types\ReceivingItemGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 *
 * @property int $receiving_id
 * @property int $item_id
 * @property int $line
 * @property float $quantity
 * @property null|string $comment
 * @property float $cost_price
 * @property float $discount_percentage
 * @property float $unit_price
 * @property null|int $old_receiving_item_id
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read null|Carbon $deleted_at
 *
 * @property-read Receiving $receiving
 * @property-read Item $item
 * @property-read InvoiceItem[] $invoiceItems
 * @property-read Inventory[] $inventory
 * @mixin \Eloquent
 */
class ReceivingItem extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ReceivingItemGraphQLType::class;

	protected $table = "receiving_items";

	protected $fillable = [
		"receiving_id",
		"item_id",
		"line",
		"quantity",
		"comment",
		"cost_price",
		"discount_percentage",
		"unit_price",
	];

	public function receiving(): BelongsTo
	{
		return $this->belongsTo(Receiving::class);
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function inventory(): HasMany
	{
		return $this->hasMany(Inventory::class);
	}
}
