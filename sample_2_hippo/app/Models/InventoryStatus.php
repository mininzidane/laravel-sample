<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\GraphQL\Types\InventoryStatusGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property-read Inventory[] $inventory
 */
class InventoryStatus extends HippoModel
{
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = InventoryStatusGraphQLType::class;

	protected $table = "inventory_statuses";

	protected $fillable = ["name"];

	public function inventory(): HasMany
	{
		return $this->hasMany(Inventory::class, "status_id", "id");
	}
}
