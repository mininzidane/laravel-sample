<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserLocation
 *
 * @property-read int $id
 *
 * @property int $user_id
 * @property int $location_id
 * @property bool $last_active
 *
 * @property-read User $user
 * @property-read Location $location
 */
class UserLocation extends HippoModel
{
	use HasFactory;

	protected $table = "tblUserLocations";

	protected $fillable = ["user_id", "location_id"];

	public $timestamps = false;

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}
}
