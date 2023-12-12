<?php

namespace App\Models;

use App\GraphQL\Types\ItemSpeciesRestrictionGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $item_id
 * @property int $species_id
 *
 * @property-read Item $item
 * @property-read Species $species
 */
class ItemSpeciesRestriction extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ItemSpeciesRestrictionGraphQLType::class;

	protected $table = "item_species_restrictions";

	protected $fillable = ["item_id", "species_id"];

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function species(): BelongsTo
	{
		return $this->belongsTo(Species::class, "species_id", "id");
	}
}
