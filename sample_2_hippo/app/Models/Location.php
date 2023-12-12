<?php

namespace App\Models;

use App\GraphQL\Types\LocationGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property int $organization_id
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $timezone
 * @property string $phone1
 * @property string $phone2
 * @property string $phone3
 * @property string $fax
 * @property string $name
 * @property string $email
 * @property bool $email_identity_verified
 * @property bool $public_domain_email
 * @property bool $primary
 * @property bool $removed
 * @property string $image_name
 * @property string $google_calendar_access_token
 * @property string $google_calendar_refresh_token
 * @property bool $google_sync
 * @property bool $google_confirm
 * @property string $google_token
 *
 * @property Organization $organization
 *
 * @mixin \Eloquent
 */
class Location extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = LocationGraphQLType::class;

	public $table = "tblOrganizationLocations";

	protected $fillable = [
		"organization_id",
		"address1",
		"address2",
		"address3",
		"city",
		"state",
		"zip",
		"country",
		"timezone",
		"phone1",
		"phone2",
		"phone3",
		"fax",
		"name",
		"email",
		"email_identity_verified",
		"public_domain_email",
		"primary",
		"removed",
		"image_name",
		"google_calendar_access_token",
		"google_calendar_refresh_token",
		"google_sync",
		"google_confirm",
		"google_token",
	];

	protected $appends = ["imageUrl"];

	public function settings(): HasMany
	{
		return $this->hasMany(LocationSetting::class);
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class);
	}

	//	public function items()
	//	{
	//		return $this->allItems();
	//	}
	//
	//	public function allItems()
	//	{
	//		return $this->hasMany(ItemLegacy::class);
	//	}

	public function resources(): HasMany
	{
		return $this->hasMany(Resource::class);
	}

	public function reminders(): HasMany
	{
		return $this->hasMany(Reminder::class);
	}

	public function sales(): HasMany
	{
		return $this->hasMany(Sale::class);
	}

	public function tz(): BelongsTo
	{
		return $this->belongsTo(Timezone::class, "timezone");
	}

	public function subregion(): BelongsTo
	{
		return $this->belongsTo(State::class, "state");
	}

	public function soapCharts(): HasMany
	{
		return $this->hasMany(SoapChart::class);
	}

	public function prescriptions(): HasMany
	{
		return $this->hasMany(Prescription::class, "location_id");
	}

	public function dispensations(): HasMany
	{
		return $this->hasMany(Dispensation::class, "location_id");
	}

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, "tblUserLocations");
	}

	public function recentUsers(): HasMany
	{
		return $this->hasMany(User::class, "last_location_id");
	}

	public function itemLocations(): HasMany
	{
		return $this->hasMany(ItemLocation::class);
	}

	public function items(): BelongsToMany
	{
		return $this->belongsToMany(Item::class, "item_locations");
	}

	public function receivings(): HasMany
	{
		return $this->hasMany(Receiving::class);
	}

	public function paymentPlatformActivations(): HasMany
	{
		return $this->hasMany(PaymentPlatformActivation::class);
	}

	public function clearentTerminals(): HasMany
	{
		return $this->hasMany(ClearentTerminal::class);
	}

	public function getImageUrlAttribute(): ?string
	{
		if (!$this->image_name) {
			return null;
		}

		return Storage::disk("s3-logos")->url($this->image_name);
	}

	public function vaccinations(): HasMany
	{
		return $this->hasMany(Vaccination::class, "location_administered");
	}

	public function getAutoSaveAttribute()
	{
		return LocationSetting::on($this->getConnectionName())
			->where("setting_name", "autosave-enabled")
			->where("location_id", $this->id)
			->get()
			->first()->setting_value ?? null;
	}
	public function setAutoSaveAttribute($value)
	{
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			["setting_name" => "autosave-enabled", "location_id" => $this->id],
			["setting_value" => $value, "location_id" => $this->id],
		);
	}

	public function getAntechActiveAttribute()
	{
		return LocationSetting::on($this->getConnectionName())
			->where("setting_name", "antech-active")
			->where("location_id", $this->id)
			->get()
			->first()->setting_value ?? null;
	}
	public function setAntechActiveAttribute($active)
	{
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			["setting_name" => "antech-active", "location_id" => $this->id],
			["setting_value" => $active],
		);
	}

	public function getAntechClinicIdAttribute()
	{
		return LocationSetting::on($this->getConnectionName())
			->where("setting_name", "antech-clinic-id")
			->where("location_id", $this->id)
			->get()
			->first()->setting_value ?? "";
	}
	public function setAntechClinicIdAttribute($id)
	{
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			["setting_name" => "antech-clinic-id", "location_id" => $this->id],
			["setting_value" => $id],
		);
	}

	public function getAntechAccountIdAttribute()
	{
		return LocationSetting::on($this->getConnectionName())
			->where("setting_name", "antech-account-id")
			->where("location_id", $this->id)
			->get()
			->first()->setting_value ?? "";
	}
	public function setAntechAccountIdAttribute($id)
	{
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			["setting_name" => "antech-account-id", "location_id" => $this->id],
			["setting_value" => $id],
		);
	}

	public function getAntechUsernameAttribute()
	{
		$value =
			LocationSetting::on($this->getConnectionName())
				->where("setting_name", "antech-client-username")
				->where("location_id", $this->id)
				->get()
				->first()->setting_value ?? null;
		if ($value) {
			return Crypt::decryptString($value);
		}
		return "";
	}
	public function setAntechUsernameAttribute($username)
	{
		$username = $username ? Crypt::encryptString($username) : "";
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			[
				"setting_name" => "antech-client-username",
				"location_id" => $this->id,
			],
			["setting_value" => $username],
		);
	}

	public function getAntechPasswordAttribute()
	{
		$value =
			LocationSetting::on($this->getConnectionName())
				->where("setting_name", "antech-client-password")
				->where("location_id", $this->id)
				->get()
				->first()->setting_value ?? null;
		if ($value) {
			return Crypt::decryptString($value);
		}
		return "";
	}
	public function setAntechPasswordAttribute($password)
	{
		$password = $password ? Crypt::encryptString($password) : "";
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			[
				"setting_name" => "antech-client-password",
				"location_id" => $this->id,
			],
			["setting_value" => $password],
		);
	}

	public function getAntechPasswordHasAttribute(): bool
	{
		$setting = LocationSetting::on($this->getConnectionName())
			->where("setting_name", "antech-client-password")
			->where("location_id", $this->id)
			->get()
			->first();
		return $setting && $setting->setting_value;
	}

	public function getZoetisActiveAttribute()
	{
		return LocationSetting::on($this->getConnectionName())
			->where("setting_name", "zoetis-client-enabled")
			->where("location_id", $this->id)
			->get()
			->first()->setting_value ?? null;
	}
	public function setZoetisActiveAttribute($active)
	{
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			[
				"setting_name" => "zoetis-client-enabled",
				"location_id" => $this->id,
			],
			["setting_value" => $active],
		);
	}

	public function getZoetisFuseIdAttribute(): string
	{
		$fuseId =
			LocationSetting::on($this->getConnectionName())
				->where("setting_name", "zoetis-fuse-id")
				->where("location_id", $this->id)
				->where("setting_value", "!=", "null")
				->get()
				->first()->setting_value ?? null;
		if ($fuseId) {
			return Crypt::decryptString($fuseId);
		}
		return "";
	}
	public function setZoetisFuseIdAttribute(string $fuseId)
	{
		$fuseId = $fuseId ? Crypt::encryptString($fuseId) : "";
		return LocationSetting::on($this->getConnectionName())->updateOrInsert(
			["setting_name" => "zoetis-fuse-id", "location_id" => $this->id],
			["setting_value" => $fuseId],
		);
	}
}
