<?php

namespace App\Models;

use App\GraphQL\Types\BreedGraphQLType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

/**
 * App\Models\Breed
 *
 * @property int $id
 * @property string $species
 * @property string $name
 */
class Breed extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = BreedGraphQLType::class;
	protected $table = "tblBreeds";

	protected $fillable = ["species", "name"];

	protected $appends = ["patient_count"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name"];

		parent::__construct($attributes);
	}

	public function getPatientCountAttribute()
	{
		return PatientBreed::on($this->getConnectionName())
			->where("breed", $this->name)
			->count();
	}

	public function speciesType()
	{
		return $this->belongsTo(Species::class, "species", "name");
	}
}
