<?php

namespace App\Models;

use App\GraphQL\Types\ItemVolumePricingGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $item_id
 * @property float $quantity
 * @property float $unit_price
 *
 * @property-read Item $item
 */
class ItemVolumePricing extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ItemVolumePricingGraphQLType::class;

	protected $table = "item_volume_pricing";

	protected $casts = [
		"quantity" => "float",
		"unit_price" => "float",
	];

	protected $fillable = ["item_id", "quantity", "unit_price"];

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}
}
