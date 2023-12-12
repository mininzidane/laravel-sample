<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $organization_id
 * @property int $client_id
 * @property int $owner_id
 * @property int $primary
 * @property int $percent
 * @property bool $removed
 * @property string $relationship_type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property-read Organization $organization
 * @property-read Patient $patient
 * @property-read Owner $owner
 * @mixin \Eloquent
 */
class PatientOwner extends Pivot
{
	use HasFactory;

	protected $table = "tblPatientOwners";

	protected $fillable = [
		"organization_id",
		"client_id",
		"owner_id",
		"primary",
		"percent",
		"removed",
		"relationship_type",
	];

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function owner(): BelongsTo
	{
		return $this->belongsTo(Owner::class, "owner_id");
	}
}
