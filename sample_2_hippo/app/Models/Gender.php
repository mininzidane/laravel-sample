<?php

namespace App\Models;

use App\GraphQL\Types\GenderGraphQLType;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $gender
 * @property string $sex
 * @property int $neutered
 * @property string $species
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 *
 * @property Species $speciesType
 */
class Gender extends HippoModel
{
	use HasTimestamps, HasName, HasFactory;
	use SoftDeletes;

	public const SEX_MALE = "M";
	public const SEX_FEMALE = "F";

	public static $graphQLType = GenderGraphQLType::class;

	protected $table = "tblGenders";

	protected $fillable = ["gender", "sex", "neutered", "species"];

	protected $casts = ["neutered" => "boolean"];

	protected $appends = ["patient_count"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["gender"];

		parent::__construct($attributes);
	}

	public function speciesType()
	{
		return $this->belongsTo(Species::class, "species", "name");
	}

	public function patients()
	{
		return $this->hasMany(Patient::class, "gender", "gender");
	}

	public function getPatientCountAttribute()
	{
		return $this->patients()->count();
	}
}
