<?php

namespace App\Models;

use App\GraphQL\Types\SchedulerSettingsGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SchedulerSettings
 *
 * @property-read int $id
 *
 * @property int $organization_id
 * @property string $start_time
 * @property string $end_time
 * @property int $unit
 * @property int $max_duration
 *
 * @property-read Organization $organization
 */
class SchedulerSettings extends HippoModel
{
	use HasFactory;

	public static $graphQLType = SchedulerSettingsGraphQLType::class;

	protected $table = "tblSchedulerSettings";

	public $timestamps = false;

	protected $fillable = [
		"organization_id",
		"start_time",
		"end_time",
		"unit",
		"max_duration",
	];

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}
}
