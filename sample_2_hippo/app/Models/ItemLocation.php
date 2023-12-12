<?php

namespace App\Models;

use App\GraphQL\Types\ItemLocationGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 *
 * @property int $item_id
 * @property int location_id
 *
 * @property-read Item $item
 * @property-read Location $location
 */
class ItemLocation extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ItemLocationGraphQLType::class;

	protected $table = "item_locations";

	protected $fillable = ["item_id", "location_id"];

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}
}
