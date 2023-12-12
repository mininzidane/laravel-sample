<?php

namespace App\Models;

use App\GraphQL\Types\EventRecurGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RRule\RRule;

/**
 * App\Models\EventRecur
 *
 * @property int $id
 * @property int $schedule_event_id
 * @property string $repeats
 * @property int $repeats_every
 * @property string $repeat_by
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property string $end_type
 * @property string $end_on
 *
 * @property Appointment $appointment
 * @property EventRecurSkip[] $skips
 * @property EventDays[] $repeatsOn
 */
class EventRecur extends HippoModel
{
	public static $graphQLType = EventRecurGraphQLType::class;
	protected $table = "tblSchedulerEventRecur";
	use HasFactory;

	protected $fillable = [
		"schedule_event_id",
		"repeats",
		"repeats_every",
		"repeat_by",
		"start_date",
		"end_type",
		"end_date",
		"end_on",
	];

	protected $hippoRepeatOptions = [
		"Daily" => [
			"freq" => "DAILY",
		],
		"Weekly" => [
			"freq" => "WEEKLY",
		],
		"Monthly" => [
			"freq" => "MONTHLY",
		],
		"Yearly" => [
			"freq" => "YEARLY",
		],
		"Every weekday (Monday to Friday)" => [
			"freq" => "DAILY",
			"byday" => "MO,TU,WE,TH,FR",
		],
		"Every Monday, Wednesday and Friday" => [
			"freq" => "DAILY",
			"byday" => "MO,WE,FR",
		],
		"Every Tuesday and Thursday" => [
			"freq" => "DAILY",
			"byday" => "TU,TH",
		],
	];

	public function appointment(): BelongsTo
	{
		return $this->belongsTo(Appointment::class, "schedule_event_id");
	}

	public function getRruleAttribute(): ?string
	{
		if (!array_key_exists($this->repeats, $this->hippoRepeatOptions)) {
			return null;
		}

		$ruleDetails = $this->hippoRepeatOptions[$this->repeats];
		$ruleDetails["interval"] = $this->repeats_every;

		switch ($this->endType) {
			case "on":
				$endDate = Carbon::parse($this->end_date);
				$endDate->addDay();
				$ruleDetails["until"] = $endDate->format("Y-m-d");
				break;
			case "after":
				$ruleDetails["count"] = $this->end_on;
				break;
			case "never":
			default:
				break;
		}

		//This will replace the byday from the above array
		$daysOfWeek = $this->repeatsOn()
			->get()
			->pluck("dayOfWeekAbbreviation")
			->toArray();
		$ruleDetails["byday"] = implode(",", $daysOfWeek);

		$rrule = new RRule($ruleDetails);

		return $rrule->rfcString();
	}

	public function skips(): HasMany
	{
		return $this->hasMany(EventRecurSkip::class, "recur_id");
	}

	public function repeatsOn(): HasMany
	{
		return $this->hasMany(
			EventDays::class,
			"event_id",
			"schedule_event_id",
		);
	}
}
