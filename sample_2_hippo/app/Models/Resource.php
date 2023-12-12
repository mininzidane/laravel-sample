<?php

namespace App\Models;

use App\GraphQL\Types\ResourceGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $organization_id
 * @property int $location_id
 * @property string $name
 * @property string $description
 * @property string $color
 *
 * @property-read User $user
 * @property-read Organization $organization
 * @property-read Location $location
 */
class Resource extends HippoModel
{
	use HasFactory;

	public static $graphQLType = ResourceGraphQLType::class;

	protected $table = "tblSchedulerResources";

	protected $fillable = ["name", "description", "color", "removed"];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class, "location_id");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function appointments(): HasMany
	{
		return $this->hasMany(Appointment::class, "resource_id");
	}
}
