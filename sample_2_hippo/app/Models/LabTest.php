<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LabTest
 *
 * @property-read int $id
 *
 * @property int $organization_id
 * @property int $lab_id
 * @property int $size
 * @property string $path
 * @property string $name
 * @property string $display_name
 * @property int $result_series_id
 * @property bool $removed
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Organization $organization
 * @property-read LabTestFolder $labTestFolder
 */
class LabTest extends HippoModel
{
	use HasFactory;

	protected $table = "tblPatientLabsTestsAttachments";

	protected $fillable = [
		"organization_id",
		"lab_id",
		"size",
		"path",
		"name",
		"display_name",
		"result_series_id",
		"removed",
	];

	public function labTestFolder(): BelongsTo
	{
		return $this->belongsTo(LabTestFolder::class, "lab_id");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}
}
