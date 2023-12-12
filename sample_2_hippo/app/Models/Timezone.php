<?php

namespace App\Models;

use App\GraphQL\Types\TimezoneGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $value
 * @property string $abbr
 * @property float $offset
 * @property bool $isdst
 * @property string $text
 * @property string $php_supported
 *
 * @property-read Location[] $locations
 * @mixin \Eloquent
 */
class Timezone extends HippoModel
{
	use HasName;
	use HasFactory;

	public static $graphQLType = TimezoneGraphQLType::class;

	protected $table = "tblTimezones";

	protected $fillable = [
		"value",
		"abbr",
		"offset",
		"isdst",
		"text",
		"php_supported",
	];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["value", "abbr"];

		parent::__construct($attributes);
	}

	public function locations(): HasMany
	{
		return $this->hasMany(Location::class, "timezone");
	}
}
