<?php

namespace App\Models;

use App\GraphQL\Types\InventoryGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Inventory
 *
 * @property-read int $id
 *
 * @property int $item_id
 * @property int $receiving_item_id
 * @property int $location_id
 * @property int $status_id
 * @property string $lot_number
 * @property string $serial_number
 * @property Carbon $expiration_date
 * @property float $starting_quantity
 * @property float $remaining_quantity
 * @property int $is_open
 * @property Carbon $opened_at
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Item $item
 * @property-read ReceivingItem $receivingItem
 * @property-read Collection|InventoryTransaction[] $inventoryTransactions
 * @property-read InventoryStatus $inventoryStatus
 * @property-read Location $location
 *
 * @property-read int $remaining
 * @property-read string $name
 * @property-read string|null $receivedAt
 *
 * @mixin \Eloquent
 */
class Inventory extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = InventoryGraphQLType::class;

	protected $table = "inventory";

	protected $fillable = [
		"item_id",
		"receiving_item_id",
		"location_id",
		"status_id",
		"lot_number",
		"serial_number",
		"expiration_date",
		"starting_quantity",
		"remaining_quantity",
		"is_open",
		"opened_at",
	];

	protected $appends = ["remaining", "name", "receivedAt"];

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_id", "id");
	}

	public function receivingItem(): BelongsTo
	{
		return $this->belongsTo(
			ReceivingItem::class,
			"receiving_item_id",
			"id",
		);
	}

	public function inventoryTransactions(): HasMany
	{
		return $this->hasMany(InventoryTransaction::class);
	}

	public function inventoryStatus(): BelongsTo
	{
		return $this->belongsTo(InventoryStatus::class, "status_id", "id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class, "location_id");
	}

	public function getRemainingAttribute(): int
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

	public function getNameAttribute(): string
	{
		$connection = $this->getConnectionName();

		if ($this->item()->first()) {
			return $this->item()->first()->name;
		}

		return "No name available";
	}

	public function getReceivedAtAttribute(): ?string
	{
		$connection = $this->getConnectionName();

		if ($this->receivingItem) {
			return Receiving::on($connection)->find(
				$this->receivingItem->receiving_id,
			)->received_at;
		} else {
			return null;
		}
	}
}
