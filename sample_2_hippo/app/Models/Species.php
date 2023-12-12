<?php

namespace App\Models;

use App\GraphQL\Types\SpeciesGraphQLType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

/**
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 */
class Species extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = SpeciesGraphQLType::class;

	protected $table = "tblSpecies";

	protected $primaryKey = "id";

	protected $fillable = ["name"];

	protected $appends = ["relationship_number"];

	public function breeds(): HasMany
	{
		return $this->hasMany(Breed::class, "species", "name");
	}

	public function genders(): HasMany
	{
		return $this->hasMany(Gender::class, "species", "name");
	}

	public function patients(): HasMany
	{
		return $this->hasMany(Patient::class, "species_id");
	}

	public function itemSpeciesRestrictions(): HasMany
	{
		return $this->hasMany(
			ItemSpeciesRestriction::class,
			"species_id",
			"id",
		);
	}

	public function getRelationshipNumberAttribute(): int
	{
		return $this->itemSpeciesRestrictions()->count() +
			$this->patients()->count();
	}
}
