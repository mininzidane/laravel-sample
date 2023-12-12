<?php

namespace App\Models;

use App\GraphQL\Types\ReceivingGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Receiving
 *
 * @property-read int $id
 *
 * @property int $location_id
 * @property int $supplier_id
 * @property int $status_id
 * @property int $user_id
 * @property int $active
 * @property Carbon $received_at
 * @property string $comment
 * @property int $old_receiving_id
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Location $location
 * @property-read Supplier $supplier
 * @property-read User $user
 * @property-read Collection|ReceivingItem[] $receivingItems
 * @property-read Collection|Item[] $items
 * @property-read ReceivingStatus $receivingStatus
 *
 * @mixin \Eloquent
 */
class Receiving extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = ReceivingGraphQLType::class;

	protected $table = "receivings";
	protected $primaryKey = "id";

	protected $fillable = [
		"status_id",
		"location_id",
		"supplier_id",
		"user_id",
		"active",
		"received_at",
		"comment",
	];

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}

	public function supplier(): BelongsTo
	{
		return $this->belongsTo(Supplier::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function receivingItems(): HasMany
	{
		return $this->hasMany(ReceivingItem::class);
	}

	public function items(): BelongsToMany
	{
		return $this->belongsToMany(Item::class, "receiving_items");
	}

	public function receivingStatus(): BelongsTo
	{
		return $this->belongsTo(ReceivingStatus::class, "status_id", "id");
	}
}
