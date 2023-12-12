<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $item_id
 * @property int $reminder_interval_id
 * @property bool $is_default
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Item $item
 * @property ReminderInterval $reminderInterval
 */
class ItemReminderInterval extends HippoModel
{
	use HasFactory;
	use SoftDeletes;

	protected $table = "item_reminder_intervals";

	protected $fillable = ["item_id", "reminder_interval_id", "is_default"];

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function reminderInterval(): BelongsTo
	{
		return $this->belongsTo(
			ReminderInterval::class,
			"reminder_interval_id",
		);
	}
}
