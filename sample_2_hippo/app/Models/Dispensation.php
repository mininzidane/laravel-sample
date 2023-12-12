<?php

namespace App\Models;

use App\GraphQL\Types\DispensationGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $prescription_id
 * @property int $user_id
 * @property int $sale_id
 * @property int $line
 * @property Carbon $issue_date
 * @property string $expiration_date
 * @property string $units
 * @property float $qty
 * @property string $note
 * @property bool $signed
 * @property int $location_id
 * @property string $timestamp
 * @property bool $removed
 * @property bool $on_estimate
 * @property bool $signed_by_original
 * @property bool $signed_by_last
 * @property string $signed_time_original
 * @property string $signed_time_last
 * @property string $last_updated
 *
 * @property-read Prescription $prescription
 * @property-read User $user
 * @property-read Location $location
 * @property-read Sale $sale
 */
class Dispensation extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = DispensationGraphQLType::class;
	protected $table = "tblMedicationDispensations";
	protected $fillable = [
		"prescription_id",
		"user_id",
		"sale_id",
		"line",
		"issue_date",
		"expiration_date",
		"units",
		"qty",
		"note",
		"signed",
		"location_id",
		"timestamp",
		"removed",
		"on_estimate",
		"signed_by_original",
		"signed_by_last",
		"signed_time_original",
		"signed_time_last",
		"last_updated",
	];

	public function prescription(): BelongsTo
	{
		return $this->belongsTo(Prescription::class, "prescription_id");
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}

	public function sale(): BelongsTo
	{
		return $this->belongsTo(Sale::class, "sale_id");
	}

	public function signedByOriginal(): BelongsTo
	{
		return $this->belongsTo(User::class, "signed_by_original");
	}

	public function signedByLast(): BelongsTo
	{
		return $this->belongsTo(User::class, "signed_by_last");
	}
}
