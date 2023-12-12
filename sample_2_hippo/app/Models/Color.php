<?php

namespace App\Models;

use App\GraphQL\Types\ColorGraphQLType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

/**
 * App\Models\Color
 *
 * @property int $id
 * @property string $species
 * @property string $name
 */
class Color extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = ColorGraphQLType::class;
	protected $table = "tblColors";

	protected $fillable = ["species", "name"];

	protected $appends = ["patient_count"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name"];

		parent::__construct($attributes);
	}

	public function getPatientCountAttribute()
	{
		return PatientColor::on($this->getConnectionName())
			->where("color", $this->name)
			->count();
	}

	public function speciesType()
	{
		return $this->belongsTo(Species::class, "species", "name");
	}
}
