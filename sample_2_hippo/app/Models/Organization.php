<?php

namespace App\Models;

use App\GraphQL\Types\OrganizationGraphQLType;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

/**
 * App\Models${:CLASS}
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $medicare_id
 * @property string $medicade_id
 * @property string $champus_id
 * @property string $npi
 * @property string $ein
 * @property string $upin
 * @property string $partner
 * @property string $salesman
 * @property int $units
 * @property string $image_name
 * @property int $phr_setup_status
 * @property bool $phr_active
 * @property int $postcards_setup_status
 * @property bool $postcards_active
 * @property int $google_calendar_setup_status
 * @property bool $google_calendar_active
 *
 * @property string|null $image_url
 * @property string|null $payment_info
 */
class Organization extends HippoModel
{
	use HasFactory;

	public static $graphQLType = OrganizationGraphQLType::class;

	protected $table = "tblOrganizations";

	protected $fillable = [
		"name",
		"email",
		"medicare_id",
		"medicade_id",
		"champus_id",
		"npi",
		"ein",
		"upin",
		"partner",
		"salesman",
		"units",
		"image_name",
		"phr_setup_status",
		"phr_active",
		"postcards_setup_status",
		"postcards_active",
		"google_calendar_setup_status",
		"google_calendar_active",
		"currency_symbol",
	];

	protected $appends = ["imageUrl", "paymentInfo"];

	public function locations()
	{
		return $this->allLocations()->where("removed", "0");
	}

	public function patients()
	{
		return $this->hasMany(Patient::class);
	}

	public function owners()
	{
		return $this->hasMany(Owner::class);
	}

	public function allLocations()
	{
		return $this->hasMany(Location::class);
	}

	public function soapCharts()
	{
		return $this->hasMany(SoapChart::class, "organization_id");
	}

	public function historyCharts()
	{
		return $this->hasMany(HistoryChart::class, "organization_id");
	}

	public function progressCharts()
	{
		return $this->hasMany(ProgressChart::class, "organization_id");
	}

	public function phoneCharts()
	{
		return $this->hasMany(PhoneChart::class, "organization_id");
	}

	public function emailCharts()
	{
		return $this->hasMany(EmailChart::class, "organization_id");
	}

	public function treatmentCharts()
	{
		return $this->hasMany(TreatmentChart::class, "organization_id");
	}

	public function suppliers()
	{
		return $this->hasMany(SupplierLegacy::class, "organization_id");
	}

	public function schedulerSettings()
	{
		return $this->hasOne(SchedulerSettings::class, "organization_id");
	}

	public function appointments()
	{
		return $this->hasMany(Appointment::class, "organization_id");
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class, "organization_id");
	}

	public function getVcpActiveAttribute()
	{
		return OrganizationSetting::on($this->getConnectionName())
			->where("setting_name", "vcp_active")
			->get()
			->first()->setting_value ?? null;
	}
	public function setVcpActiveAttribute($active)
	{
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "vcp_active"],
			["setting_value" => $active],
		);
	}

	public function getVcpUserNameAttribute()
	{
		$value =
			OrganizationSetting::on($this->getConnectionName())
				->where("setting_name", "vcp_username")
				->get()
				->first()->setting_value ?? null;
		if ($value) {
			return Crypt::decryptString($value);
		}
		return "";
	}
	public function setVcpUserNameAttribute($username)
	{
		$username = $username ? Crypt::encryptString($username) : "";
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "vcp_username"],
			["setting_value" => $username],
		);
	}

	public function getVcpPasswordAttribute()
	{
		$value =
			OrganizationSetting::on($this->getConnectionName())
				->where("setting_name", "vcp_password")
				->get()
				->first()->setting_value ?? null;
		if ($value) {
			return Crypt::decryptString($value);
		}
		return "";
	}
	public function setVcpPasswordAttribute($password)
	{
		$password = $password ? Crypt::encryptString($password) : "";
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "vcp_password"],
			["setting_value" => $password],
		);
	}

	public function getEstimateStatementAttribute()
	{
		return OrganizationSetting::on($this->getConnectionName())
			->where("setting_name", "estimate_statement")
			->get()
			->first()->setting_value ?? null;
	}
	public function setEstimateStatementAttribute($value)
	{
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "estimate_statement"],
			["setting_value" => $value],
		);
	}

	public function getPaymentInfoAttribute()
	{
		return OrganizationSetting::on($this->getConnectionName())
			->where("setting_name", "payment_info")
			->get()
			->first()->setting_value ?? null;
	}
	public function setPaymentInfoAttribute($value)
	{
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "payment_info"],
			["setting_value" => $value],
		);
	}

	public function getReturnPolicyAttribute()
	{
		return OrganizationSetting::on($this->getConnectionName())
			->where("setting_name", "return_policy")
			->get()
			->first()->setting_value ?? null;
	}
	public function setReturnPolicyAttribute($value)
	{
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "return_policy"],
			["setting_value" => $value],
		);
	}

	public function getAccountStatusAttribute(): int
	{
		$connection = $this->getConnectionName();
		$setting = OrganizationSetting::on($connection)->find("account-status");
		return (int) $setting->setting_value;
	}

	public function getTrialExpiredAttribute(): bool
	{
		$connection = $this->getConnectionName();

		if ($this->accountStatus != 1) {
			return false;
		}

		$trialStartDateSetting = OrganizationSetting::on($connection)->find(
			"trial-start-date",
		);
		$trialLengthSetting = OrganizationSetting::on($connection)->find(
			"trial-length-in-days",
		);

		$trialStart = Carbon::parse(
			$trialStartDateSetting->setting_value ?? "now",
		);
		$trialExpiration = $trialStart->addDays(
			$trialLengthSetting->setting_value ?? 0,
		);

		return $trialExpiration->lt(Carbon::now());
	}

	public function getImageUrlAttribute()
	{
		if (!$this->image_name) {
			return null;
		}

		return Storage::disk("s3-logos")->url($this->image_name);
	}

	public function getIdexxActiveAttribute()
	{
		return OrganizationSetting::on($this->getConnectionName())
			->where("setting_name", "idexx-enabled")
			->get()
			->first()->setting_value ?? null;
	}
	public function setIdexxActiveAttribute($active)
	{
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "idexx-enabled"],
			["setting_value" => $active],
		);
	}

	public function getIdexxUsernameAttribute()
	{
		$value =
			OrganizationSetting::on($this->getConnectionName())
				->where("setting_name", "idexx-username")
				->get()
				->first()->setting_value ?? null;
		if ($value) {
			return Crypt::decryptString($value);
		}
		return "";
	}
	public function setIdexxUsernameAttribute($username)
	{
		$username = $username ? Crypt::encryptString($username) : "";
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "idexx-username"],
			["setting_value" => $username],
		);
	}

	public function getIdexxPasswordAttribute()
	{
		$value =
			OrganizationSetting::on($this->getConnectionName())
				->where("setting_name", "idexx-password")
				->get()
				->first()->setting_value ?? null;
		if ($value) {
			return Crypt::decryptString($value);
		}
		return "";
	}
	public function setIdexxPasswordAttribute($password)
	{
		$password = $password ? Crypt::encryptString($password) : "";
		return OrganizationSetting::on(
			$this->getConnectionName(),
		)->updateOrInsert(
			["setting_name" => "idexx-password"],
			["setting_value" => $password],
		);
	}
}
