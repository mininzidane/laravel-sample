<?php

namespace App\Models;

use App\GraphQL\Types\ItemReplacesGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $item_id
 * @property int $replaces_item_id
 *
 * @property-read Item $replaces
 * @property-read Item $replaced
 */
class ItemReplaces extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ItemReplacesGraphQLType::class;

	protected $table = "item_replaces";

	protected $fillable = ["item_id", "replaces_item_id"];

	public function replaces(): BelongsTo
	{
		return $this->belongsTo(Item::class, "replaces_item_id");
	}

	public function replaced(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_id");
	}
}
