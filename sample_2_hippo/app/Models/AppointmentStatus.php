<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\GraphQL\Types\AppointmentStatusGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

/**
 * App\Models\AppointmentStatus
 *
 * @property string $status_key
 * @property string $status_name
 * @property bool $in_hospital_status
 * @property bool $last_visit_status
 * @property bool $default_status
 * @property bool $hidden
 * @property bool $check_out_status
 * @property bool $sale_complete_default_status
 */
class AppointmentStatus extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = AppointmentStatusGraphQLType::class;
	protected $table = "tblAppointmentStatuses";
	protected $primaryKey = "status_key";
	public $incrementing = false;

	protected $fillable = [
		"status_key",
		"status_name",
		"in_hospital_status",
		"last_visit_status",
		"default_status",
		"hidden",
		"check_out_status",
		"sale_complete_default_status",
	];

	public function appointments()
	{
		return $this->hasMany(Appointment::class, "status", "status_key");
	}
}
