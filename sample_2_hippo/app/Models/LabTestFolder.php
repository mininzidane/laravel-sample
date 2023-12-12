<?php

namespace App\Models;

use Barryvdh\Reflection\DocBlock\Type\Collection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\LabTestFolder
 *
 * @property-read int $id
 *
 * @property int $client_id
 * @property int $organization_id
 * @property int $added_by
 * @property Carbon $date
 * @property string $desc
 * @property string|null $title
 * @property bool $removed
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Organization $organization
 * @property-read Patient $patient
 * @property-read User $addedBy
 * @property-read Collection|LabTest[] $attachments
 */
class LabTestFolder extends HippoModel
{
	use HasFactory;

	protected $table = "tblPatientLabsTests";

	protected $fillable = [
		"client_id",
		"organization_id",
		"added_by",
		"date",
		"desc",
		"title",
		"removed",
	];

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function addedBy(): BelongsTo
	{
		return $this->belongsTo(User::class, "added_by");
	}

	public function attachments(): HasMany
	{
		return $this->hasMany(LabTest::class, "lab_id");
	}
}
