<?php

namespace App\Models;

use App\GraphQL\Types\EventRecurSkipGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RRule\RRule;

/**
 * App\Models\EventRecurSkip
 *
 * @property int $id
 * @property int $recur_id
 * @property \Carbon\Carbon $start_time
 * @property \Carbon\Carbon $end_date
 * @property array $hippoRepeatOptions
 * @property string $repeats
 * @property string $repeats_every
 * @property string $endType
 * @property string $end_on
 *
 * @property EventRecur $recurrence
 */
class EventRecurSkip extends HippoModel
{
	use HasFactory;

	public static $graphQLType = EventRecurSkipGraphQLType::class;
	protected $table = "tblSchedulerRecurSkip";

	protected $fillable = ["recur_id", "start_time"];

	public function recurrence(): BelongsTo
	{
		return $this->belongsTo(EventRecur::class, "recur_id");
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

		$rrule = new RRule($ruleDetails);

		return $rrule->rfcString();
	}
}
