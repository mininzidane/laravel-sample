<?php

namespace App\Models;

use App\GraphQL\Types\TreatmentSheetTreatmentGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $schedule_event_id
 * @property string $treatment_name
 * @property int $client_id
 * @property int $item_id
 * @property int $sale_id
 * @property int $line
 * @property float $qty
 * @property int $assign_to_user_id
 * @property \DateTime $due
 * @property string $removed_reason
 * @property string $rejected_reason
 * @property bool $rejected
 * @property bool $completed
 * @property \DateTime $completed_time
 * @property string $chart_note
 * @property string $recur
 * @property \DateTime $recur_next_due
 * @property bool $removed
 * @property string $updated_at
 * @property string $created_at
 * @property string $deleted_at
 *
 * @property-read Appointment $appointment
 * @property-read Patient $patient
 * @property-read Item $item
 * @property-read Invoice $invoice
 * @property-read User $user
 * @mixin \Eloquent
 */
class TreatmentSheetTreatment extends HippoModel
{
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = TreatmentSheetTreatmentGraphQLType::class;

	protected $table = "tblTreatmentSheetTreatments";

	protected $fillable = [
		"schedule_event_id",
		"treatment_name",
		"client_id",
		"item_id",
		"sale_id",
		"line",
		"qty",
		"assign_to_user_id",
		"due",
		"removed_reason",
		"rejected_reason",
		"rejected",
		"completed",
		"completed_time",
		"chart_note",
		"recur",
		"recur_next_due",
		"removed",
	];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$this->nameFields = ["treatment_name"];
	}

	public function appointment(): BelongsTo
	{
		return $this->belongsTo(Appointment::class, "schedule_event_id");
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_id");
	}

	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class, "sale_id");
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "assign_to_user_id");
	}
}
