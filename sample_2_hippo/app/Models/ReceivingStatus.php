<?php

namespace App\Models;

use App\GraphQL\Types\ReceivingStatusGraphQLType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 *
 * @property-read Receiving[] $receivings
 * @mixin \Eloquent
 */
class ReceivingStatus extends HippoModel
{
	use SoftDeletes;
	use HasName;

	public static $graphQLType = ReceivingStatusGraphQLType::class;

	protected $table = "receiving_statuses";

	protected $fillable = ["name"];

	public function receivings(): HasMany
	{
		return $this->hasMany(Receiving::class, "status_id", "id");
	}
}
