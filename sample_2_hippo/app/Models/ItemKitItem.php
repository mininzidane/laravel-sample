<?php

namespace App\Models;

use App\GraphQL\Types\ItemKitItemGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ItemKitItem
 *
 * @property-read int $id
 *
 * @property int $item_kit_id
 * @property int $item_id
 * @property float $quantity
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Item $itemKit
 * @property-read Item $item
 */
class ItemKitItem extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = ItemKitItemGraphQLType::class;

	protected $table = "item_kit_items";

	protected $fillable = ["item_kit_id", "item_id", "quantity"];

	public function itemKit(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_kit_id");
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_id");
	}
}
