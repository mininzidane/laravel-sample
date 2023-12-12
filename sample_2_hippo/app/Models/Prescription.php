<?php

namespace App\Models;

use App\GraphQL\Types\PrescriptionGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $item_id
 * @property int $client_id
 * @property int $refills_left
 * @property int $refills_original
 * @property int $user_id
 * @property bool $acute
 * @property string $chart_note
 * @property int $organization_id
 * @property int $location_id
 * @property bool $removed
 * @property int $timestamp
 *
 * @property-read Patient $patient
 * @property-read Item $item
 * @property-read User $user
 * @property-read Location $location
 * @property-read Organization $organization
 * @property-read Dispensation $dispensation
 */
class Prescription extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = PrescriptionGraphQLType::class;

	protected $table = "tblMedicationPrescriptions";

	protected $fillable = [
		"item_id",
		"client_id",
		"refills_left",
		"refills_original",
		"user_id",
		"acute",
		"chart_note",
		"organization_id",
		"location_id",
		"removed",
		"timestamp",
	];

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_id");
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, "user_id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class);
	}

	public function dispensations(): HasMany
	{
		return $this->hasMany(Dispensation::class, "prescription_id");
	}
}
