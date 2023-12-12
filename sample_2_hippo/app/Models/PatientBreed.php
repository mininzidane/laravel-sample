<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PatientBreed
 *
 * @property-read int $id
 *
 * @property int $client_id
 * @property string $breed
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Patient $patient
 * @property-read Breed $breedModel
 */
class PatientBreed extends HippoModel
{
	use SoftDeletes;
	use HasName, HasFactory;

	public static $graphQLType = null;

	protected $table = "tblPatientAnimalBreeds";

	public $timestamps = false;

	protected $fillable = ["client_id", "breed"];

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function breedModel(): BelongsTo
	{
		return $this->belongsTo(Breed::class, "breed", "name");
	}
}
