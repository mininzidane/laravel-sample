<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\LabRequisition
 *
 * @property-read int $id
 *
 * @property int $user_id
 * @property int $veterinarian_id
 * @property int $client_id
 * @property int $location_id
 * @property int $order_code_id
 * @property string $custom_order_code
 * @property string $integration
 * @property string $status
 * @property bool $reviewed
 * @property bool $removed
 *
 * @property-read User $user
 * @property-read User $veterinarian
 * @property-read Patient $patient
 * @property-read Location $location
 * @property-read AntechOrderCode $antechOrderCode
 * @property-read ZoetisOrderCode $zoetisOrderCode
 */
class LabRequisition extends HippoModel
{
	use SoftDeletes, HasFactory;

	protected $table = "tblRequisitions";

	protected $fillable = [
		"user_id",
		"veterinarian_id",
		"client_id",
		"location_id",
		"order_code_id",
		"custom_order_code",
		"integration",
		"status",
		"reviewed",
		"removed",
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}

	public function veterinarian(): BelongsTo
	{
		return $this->belongsTo(User::class, "veterinarian_id");
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class, "location_id");
	}

	public function antechOrderCode(): HasOne
	{
		return $this->hasOne(AntechOrderCode::class, "id", "order_code_id");
	}

	public function zoetisOrderCode(): HasOne
	{
		return $this->hasOne(ZoetisOrderCode::class, "id", "order_code_id");
	}

	public function scopeWaiting($query)
	{
		return $query->where("status", "=", "INITIAL");
	}
}
