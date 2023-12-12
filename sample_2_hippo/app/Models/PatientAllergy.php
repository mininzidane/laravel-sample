<?php

namespace App\Models;

use App\GraphQL\Types\PatientAllergyGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PatientAllergy
 *
 * @property-read int $id
 *
 * @property int $client_id
 * @property string $allergy
 * @property int $removed
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Patient $patient
 * @property-read Allergy $allergyModel
 */
class PatientAllergy extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = PatientAllergyGraphQLType::class;

	protected $table = "tblPatientAllergies";

	protected $primaryKey = "id";

	protected $fillable = ["client_id", "allergy", "removed", "deleted_at"];

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function allergyModel(): BelongsTo
	{
		return $this->belongsTo(Allergy::class, "allergy", "name");
	}
}
