<?php

namespace App\Models;

use App\GraphQL\Types\MarkingsGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Markings
 *
 * @property-read int $id
 *
 * @property string $species
 * @property string $name
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read int $patientCount
 *
 * @property-read Species $speciesType
 */
class Markings extends HippoModel
{
	use HasTimestamps, HasName, HasFactory;
	use SoftDeletes;

	public static $graphQLType = MarkingsGraphQLType::class;
	protected $table = "tblMarkings";

	protected $fillable = ["species", "name"];

	protected $appends = ["patient_count"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name"];

		parent::__construct($attributes);
	}

	public function getPatientCountAttribute(): int
	{
		return PatientMarkings::on($this->getConnectionName())
			->where("markings", $this->name)
			->count();
	}

	public function speciesType(): BelongsTo
	{
		return $this->belongsTo(Species::class, "species", "name");
	}
}
