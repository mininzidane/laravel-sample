<?php

namespace App\Models;

use App\GraphQL\Types\PatientAllergyNoteGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PatientAllergyNote
 *
 * @property int $client_id
 * @property string $note
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Patient $patient
 */
class PatientAllergyNote extends HippoModel
{
	use HasFactory;

	public static $graphQLType = PatientAllergyNoteGraphQLType::class;

	protected $table = "tblPatientAllergiesNotes";

	protected $primaryKey = "client_id";
	public $incrementing = false;

	protected $fillable = ["client_id", "note"];

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}
}
