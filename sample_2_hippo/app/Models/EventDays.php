<?php

namespace App\Models;

use App\GraphQL\Types\EventDaysGraphQLType;
use App\GraphQL\Types\EventRecurGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RRule\RRule;

/**
 *
 * @property int $event_id
 * @property int $day
 */
class EventDays extends HippoModel
{
	public static $graphQLType = EventDaysGraphQLType::class;
	protected $table = "tblSchedulerEventDays";
	protected $primaryKey = "event_id";
	protected $appends = ["dayOfWeekAbbreviation"];
	use HasFactory;
	public $timestamps = false;

	protected $fillable = ["event_id", "day"];

	public function getDayOfWeekAbbreviationAttribute()
	{
		$daysOfWeek = [
			0 => "SU",
			1 => "MO",
			2 => "TU",
			3 => "WE",
			4 => "TH",
			5 => "FR",
			6 => "SA",
		];

		return isset($daysOfWeek[$this->day]) ? $daysOfWeek[$this->day] : "";
	}

	public function appointment()
	{
		return $this->belongsTo(Appointment::class, "event_id");
	}
}
