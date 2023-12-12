<?php

namespace App\Models;

use App\GraphQL\Types\ItemReminderReplacesGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $item_id
 * @property int $replaces_item_id
 *
 * @property-read Item $replacedItem
 * @property-read Item $newItem
 */
class ItemReminderReplaces extends HippoModel
{
	use HasFactory;

	public static $graphQLType = ItemReminderReplacesGraphQLType::class;

	protected $table = "item_replaces";

	protected $fillable = ["item_id", "replaces_item_id"];

	public function replacedItem(): BelongsTo
	{
		return $this->belongsTo(Item::class, "replaces_item_id");
	}

	public function newItem(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_id");
	}
}
