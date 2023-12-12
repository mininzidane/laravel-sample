<?php

namespace App\Models;

use App\GraphQL\Types\PatientAlertGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PatientAlert
 *
 * @property-read int $id
 *
 * @property int $client_id
 * @property int $organization_id
 * @property int $added_by
 * @property Carbon $date_added
 * @property string $description
 * @property int $current
 * @property int $removed
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Owner $owner
 * @property-read Patient $patient
 * @property-read User $addedBy
 * @property-read Organization $organization
 */
class PatientAlert extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = PatientAlertGraphQLType::class;

	protected $table = "tblPatientAlerts";

	protected $fillable = [
		"client_id",
		"organization_id",
		"added_by",
		"description",
		"current",
		"removed",
	];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}

	public function owner(): BelongsTo
	{
		return $this->belongsTo(Owner::class, "owner_id")->withDefault([
			"id" => null,
			"first_name" => "",
			"middle_name" => "",
			"last_name" => "",
			"full_name" => "",
		]);
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function addedBy(): BelongsTo
	{
		return $this->belongsTo(User::class, "added_by");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}
}
