<?php

namespace App\Models;

use App\GraphQL\Types\EventGraphQLType;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $resource_id
 * @property int $organization_id
 * @property string $name
 * @property string $description
 * @property int $length
 * @property int $type_id
 * @property string $note_type
 * @property bool $removed
 *
 * @property-read EventType $type
 * @property-read Resource $resource
 * @property-read Appointment[] $appointments
 * @property-read Organization $organization
 */
class Event extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = EventGraphQLType::class;
	protected $table = "tblSchedulerEvents";

	protected $fillable = [
		"resource_id",
		"organization_id",
		"name",
		"description",
		"length",
		"type_id",
		"note_type",
		"removed",
	];

	public function type(): BelongsTo
	{
		return $this->belongsTo(EventType::class);
	}

	public function resource(): BelongsTo
	{
		return $this->belongsTo(Resource::class, "resource_id");
	}

	public function appointments(): HasMany
	{
		return $this->hasMany(Appointment::class, "event_id");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}
}
