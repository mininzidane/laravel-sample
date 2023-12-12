<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PatientColor
 *
 * @property-read int $id
 *
 * @property int $client_id
 * @property string $color
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Patient $patient
 * @property-read Color $colorModel
 */
class PatientColor extends HippoModel
{
	use SoftDeletes;
	use HasName, HasFactory;

	public static $graphQLType = null;

	protected $table = "tblPatientAnimalColors";

	protected $fillable = ["client_id", "color"];

	public $timestamps = false;

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function colorModel(): BelongsTo
	{
		return $this->belongsTo(Color::class, "color", "name");
	}
}
