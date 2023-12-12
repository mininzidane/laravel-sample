<?php

namespace App\Models;

use App\GraphQL\Types\PatientImageGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\PatientImage
 *
 * @property-read int $id
 *
 * @property int $client_id
 * @property string $image_type
 * @property string $size
 * @property string $image_ctgy
 * @property string $name
 * @property string $path
 * @property string $description
 * @property bool $removed
 * @property int $organization_id
 *
 * @property-read string $presignedUrl
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Patient $patient
 * @property-read Organization $organization
 */
class PatientImage extends HippoModel
{
	use HasTimestamps, HasName, HasFactory;
	use SoftDeletes;

	public static $graphQLType = PatientImageGraphQLType::class;

	protected $table = "tblPatientImages";

	protected $fillable = [
		"client_id",
		"image_type",
		"size",
		"image_ctgy",
		"name",
		"path",
		"description",
		"removed",
		"organization_id",
	];

	protected $appends = ["presignedUrl"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name"];

		parent::__construct($attributes);
	}

	public function getPresignedUrlAttribute(): string
	{
		$patientImage = PatientImage::on($this->connection)->find($this->id);

		if (empty($patientImage)) {
			$url = "/img/hippo-avatar.svg";
		} else {
			$url = Storage::disk("s3-photos")->temporaryUrl(
				$patientImage->name,
				now()->addMinutes(10),
			);
		}

		return $url;
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "id", "client_id");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}
}
