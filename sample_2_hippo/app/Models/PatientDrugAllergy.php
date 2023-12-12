<?php

namespace App\Models;

use App\GraphQL\Types\PatientDrugAllergyGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PatientDrugAllergy
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
 * @property-read Allergy $drugAllergy
 */
class PatientDrugAllergy extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = PatientDrugAllergyGraphQLType::class;

	protected $table = "tblPatientDrugAllergies";

	protected $primaryKey = "id";

	protected $fillable = ["client_id", "allergy", "removed", "deleted_at"];

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function drugAllergy(): BelongsTo
	{
		return $this->belongsTo(Allergy::class, "allergy", "name");
	}
}
