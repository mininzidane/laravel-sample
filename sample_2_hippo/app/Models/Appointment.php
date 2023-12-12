<?php

namespace App\Models;

use App\GraphQL\Types\AppointmentGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Appointment
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int $creator_id
 * @property int $resource_id
 * @property int $client_id
 * @property int $event_id
 * @property int $type_id
 * @property Carbon\Carbon $start_time
 * @property string $status
 * @property int $duration
 * @property string $color
 * @property bool $removed
 * @property string $description
 * @property string $name
 * @property bool $blocked
 * @property string $google_calendar_event_id
 * @property bool $google_can_edit
 * @property Carbon\Carbon $check_in_time
 * @property Carbon\Carbon $check_out_time
 * @property string $google_message_number
 * @property int $updated_by
 */
class Appointment extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = AppointmentGraphQLType::class;
	protected $table = "tblSchedule";

	protected $fillable = [
		"organization_id",
		"user_id",
		"creator_id",
		"resource_id",
		"client_id",
		"event_id",
		"type_id",
		"start_time",
		"status",
		"duration",
		"color",
		"removed",
		"description",
		"name",
		"blocked",
		"google_calendar_event_id",
		"google_can_edit",
		"check_in_time",
		"check_out_time",
		"google_message_number",
		"updated_by",
	];

	public function __construct(array $attributes = [])
	{
		$this->primaryDateField = "start_time";

		parent::__construct($attributes);
	}

	public function creator()
	{
		return $this->belongsTo(User::class, "creator_id");
	}

	public function user()
	{
		return $this->belongsTo(User::class, "user_id");
	}

	public function resource()
	{
		return $this->belongsTo(Resource::class, "resource_id");
	}

	public function event()
	{
		return $this->belongsTo(Event::class, "event_id");
	}

	public function type()
	{
		return $this->belongsTo(EventType::class);
	}

	public function patient()
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function recur()
	{
		return $this->hasOne(EventRecur::class, "schedule_event_id");
	}

	public function eventDays()
	{
		return $this->hasMany(EventDays::class, "event_id");
	}

	public function appointmentStatus()
	{
		return $this->belongsTo(
			AppointmentStatus::class,
			"status",
			"status_key",
		);
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function treatmentSheetTreatments()
	{
		return $this->hasMany(
			TreatmentSheetTreatment::class,
			"schedule_event_id",
		);
	}
}
