<?php

namespace App\Models;

use App\GraphQL\Types\SettingGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LocationSetting
 *
 * @property int $location_id
 * @property string $setting_name
 * @property string $setting_value
 *
 * @property-read Location $location
 */
class LocationSetting extends HippoModel
{
	use HasFactory;

	public static $graphQLType = SettingGraphQLType::class;

	protected $table = "tblPracticeLocationSettings";

	protected $primaryKey = null;

	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = ["location_id", "setting_name", "setting_value"];

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}
}
