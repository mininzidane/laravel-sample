<?php

namespace App\Models;

use App\GraphQL\Types\EventTypeGraphQLType;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string $description
 * @property string $color
 * @property int $organization_id
 * @property bool $removed
 *
 * @property-read Organization $organization
 * @property-read Appointment[] appointments
 */
class EventType extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = EventTypeGraphQLType::class;
	protected $table = "tblSchedulerEventTypes";

	protected $fillable = [
		"name",
		"description",
		"color",
		"organization_id",
		"removed",
	];

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function appointments(): HasMany
	{
		return $this->hasMany(Appointment::class, "type_id");
	}
}
