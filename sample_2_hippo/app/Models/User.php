<?php

namespace App\Models;

use App\GraphQL\Types\UserGraphQLType;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property int $organization_id
 * @property bool $administrator
 * @property bool $active
 * @property string $degree
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $specialty
 * @property string $phone1
 * @property string $phone1_ext
 * @property bool $drug_alerts_view_mild
 * @property bool $drug_alerts_view_moderate
 * @property bool $drug_alerts_view_severe
 * @property bool $allergy_alerts_view_mild
 * @property bool $allergy_alerts_view_moderate
 * @property bool $allergy_alerts_view_severe
 * @property bool $list_appointment_scheduler
 * @property bool $enroll_template_store
 * @property bool $enroll_patients_phr
 * @property string $npi
 * @property string $ein
 * @property string $upin
 * @property string $license
 * @property string $dea
 * @property bool $removed
 * @property bool $first_login
 * @property string $sec_question
 * @property string $sec_answer
 * @property bool $agree_terms
 * @property string $title
 * @property string $organization_role
 * @property string $landing
 * @property int $last_client
 * @property int $last_location_id
 * @property string $sig_name
 * @property Carbon $created
 * @property Carbon $updated
 * @property bool $hidden
 * @property bool $email_verified
 * @property Carbon $email_verified_timestamp
 * @property string $cliented_username
 * @property string $cliented_password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @property-read Resource[] $resources
 * @property-read Appointment[] $appointments
 * @property-read Appointment[] $createdAppointments
 * @property-read Patient[] $addedPatients
 * @property-read LineItem[] $lineItems
 * @property-read SoapChart[] $soapCharts
 * @property-read SoapChart[] $soapChartsSeen
 * @property-read SoapChart $soapChartsLastSigned
 * @property-read SoapChart $soapChartsOriginallySigned
 * @property-read HistoryChart[] $historyCharts
 * @property-read HistoryChart[] $historyChartsSeen
 * @property-read HistoryChart $historyChartsLastSigned
 * @property-read HistoryChart $historyChartsOriginallySigned
 * @property-read PhoneChart[] $phoneCharts
 * @property-read PhoneChart $phoneChartsLastSigned
 * @property-read PhoneChart $phoneChartsOriginallySigned
 * @property-read EmailChart[] $emailCharts
 * @property-read EmailChart[] $emailChartsSeen
 * @property-read EmailChart $emailChartsLastSigned
 * @property-read EmailChart $emailChartsOriginallySigned
 * @property-read ProgressChart[] $progressCharts
 * @property-read ProgressChart[] $progressChartsSeen
 * @property-read ProgressChart $progressChartsLastSigned
 * @property-read ProgressChart $progressChartsOriginallySigned
 * @property-read TreatmentChart[] $treatmentCharts
 * @property-read TreatmentChart[] $treatmentChartsSeen
 * @property-read TreatmentChart $treatmentChartsLastSigned
 * @property-read TreatmentChart $treatmentChartsOriginallySigned
 * @property-read ReceivingLegacy[] $receivingsLegacy
 * @property-read ReceivingLegacy[] $receivings
 * @property-read SupplierLegacy[] $suppliersLegacy
 * @property-read Prescription[] $prescriptions
 * @property-read Dispensation[] $dispensations
 * @property-read Dispensation[] $originallySignedDispensations
 * @property-read Dispensation[] $lastSignedDispensations
 * @property-read Patient $lastClient
 * @property-read Location $lastLocation
 * @property-read Location[] $locations
 * @property-read Organization $organization
 * @property-read InventoryTransaction[] $inventoryTransactions
 * @property-read ClearentTransaction[] $clearentTransactions
 * @property-read InvoiceItem[] $invoiceItems
 * @property-read TreatmentSheetTreatment[] $treatmentSheetTreatments
 * @property-read Vaccination[] $vaccinationsLastModified
 * @property-read Vaccination[] $vaccinationsAdministered
 */
class User extends HippoModel implements
	AuthenticatableContract,
	AuthorizableContract,
	CanResetPasswordContract
{
	// Base Model
	use SoftDeletes, HasEmailAddress, HasPhoneNumber, HasName, HasFactory;

	// Authentication
	use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

	// Tokens
	use HasApiTokens, Notifiable, HasRoles;

	public static $graphQLType = UserGraphQLType::class;

	protected $table = "tblUsers";

	protected $fillable = [
		"username",
		"password",
		"salt",
		"organization_id",
		"administrator",
		"active",
		"degree",
		"first_name",
		"last_name",
		"email",
		"specialty",
		"phone1",
		"phone1_ext",
		"drug_alerts_view_mild",
		"drug_alerts_view_moderate",
		"drug_alerts_view_severe",
		"allergy_alerts_view_mild",
		"allergy_alerts_view_moderate",
		"allergy_alerts_view_severe",
		"list_appointment_scheduler",
		"enroll_template_store",
		"enroll_patients_phr",
		"npi",
		"ein",
		"upin",
		"license",
		"dea",
		"removed",
		"first_login",
		"sec_question",
		"sec_answer",
		"agree_terms",
		"title",
		"organization_role",
		"landing",
		"last_client",
		"last_location_id",
		"sig_name",
		"created",
		"updated",
		"hidden",
		"email_verified",
		"email_verified_timestamp",
		"cliented_username",
		"cliented_password",
	];

	protected $appends = ["full_name", "isProvider"];

	public $casts = [
		"cliented_username" => "encrypted",
		"cliented_password" => "encrypted",
	];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["first_name", "last_name"];

		$this->phoneNumberFieldName = "phone1";

		parent::__construct($attributes);
	}

	/**
	 * Get the salt for the user.
	 *
	 * @return string
	 */
	public function getAuthSalt(): string
	{
		return $this->salt;
	}

	public function resources(): HasMany
	{
		return $this->hasMany(Resource::class, "user_id");
	}

	public function appointments(): HasMany
	{
		return $this->hasMany(Appointment::class, "user_id");
	}

	public function createdAppointments(): HasMany
	{
		return $this->hasMany(Appointment::class, "creator_id");
	}

	public function addedPatients(): HasMany
	{
		return $this->hasMany(Patient::class, "added_by", "id");
	}

	public function lineItems(): HasMany
	{
		return $this->hasMany(LineItem::class, "seenby_id");
	}

	public function soapCharts(): HasMany
	{
		return $this->hasMany(SoapChart::class, "user_id");
	}

	public function soapChartsSeen(): HasMany
	{
		return $this->hasMany(SoapChart::class, "seen_by");
	}

	public function soapChartsLastSigned(): BelongsTo
	{
		return $this->belongsTo(SoapChart::class, "signed_by_last");
	}

	public function soapChartsOriginallySigned(): BelongsTo
	{
		return $this->belongsTo(SoapChart::class, "signed_by_original");
	}

	public function historyCharts(): HasMany
	{
		return $this->hasMany(HistoryChart::class, "user_id");
	}

	public function historyChartsSeen(): HasMany
	{
		return $this->hasMany(HistoryChart::class, "seen_by");
	}

	public function historyChartsLastSigned(): BelongsTo
	{
		return $this->belongsTo(HistoryChart::class, "signed_by_last");
	}

	public function historyChartsOriginallySigned(): BelongsTo
	{
		return $this->belongsTo(HistoryChart::class, "signed_by_original");
	}

	public function phoneCharts(): HasMany
	{
		return $this->hasMany(PhoneChart::class, "user_id");
	}

	public function phoneChartsSeen(): HasMany
	{
		return $this->hasMany(PhoneChart::class, "seen_by");
	}

	public function phoneChartsLastSigned(): BelongsTo
	{
		return $this->belongsTo(PhoneChart::class, "signed_by_last");
	}

	public function phoneChartsOriginallySigned(): BelongsTo
	{
		return $this->belongsTo(PhoneChart::class, "signed_by_original");
	}

	public function emailCharts(): HasMany
	{
		return $this->hasMany(EmailChart::class, "user_id");
	}

	public function emailChartsSeen(): HasMany
	{
		return $this->hasMany(EmailChart::class, "seen_by");
	}

	public function emailChartsLastSigned(): BelongsTo
	{
		return $this->belongsTo(EmailChart::class, "signed_by_last");
	}

	public function emailChartsOriginallySigned(): BelongsTo
	{
		return $this->belongsTo(EmailChart::class, "signed_by_original");
	}

	public function progressCharts(): HasMany
	{
		return $this->hasMany(ProgressChart::class, "user_id");
	}

	public function progressChartsSeen(): HasMany
	{
		return $this->hasMany(ProgressChart::class, "seen_by");
	}

	public function progressChartsLastSigned(): BelongsTo
	{
		return $this->belongsTo(ProgressChart::class, "signed_by_last");
	}

	public function progressChartsOriginallySigned(): BelongsTo
	{
		return $this->belongsTo(ProgressChart::class, "signed_by_original");
	}

	public function treatmentCharts(): HasMany
	{
		return $this->hasMany(TreatmentChart::class, "user_id");
	}

	public function treatmentChartsSeen(): HasMany
	{
		return $this->hasMany(TreatmentChart::class, "seen_by");
	}

	public function treatmentChartsLastSigned(): BelongsTo
	{
		return $this->belongsTo(TreatmentChart::class, "signed_by_last");
	}

	public function treatmentChartsOriginallySigned(): BelongsTo
	{
		return $this->belongsTo(TreatmentChart::class, "signed_by_original");
	}

	public function receivingsLegacy(): HasMany
	{
		return $this->hasMany(ReceivingLegacy::class, "employee_id");
	}

	public function receivings(): HasMany
	{
		return $this->hasMany(Receiving::class);
	}

	public function suppliersLegacy(): HasMany
	{
		return $this->hasMany(SupplierLegacy::class, "person_id");
	}

	public function prescriptions(): HasMany
	{
		return $this->hasMany(Prescription::class, "user_id");
	}

	public function dispensations(): HasMany
	{
		return $this->hasMany(Dispensation::class, "user_id");
	}

	public function originallySignedDispensations(): HasMany
	{
		return $this->hasMany(Dispensation::class, "signed_by_original");
	}

	public function lastSignedDispensations(): HasMany
	{
		return $this->hasMany(Dispensation::class, "signed_by_last");
	}

	public function lastClient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "last_client", "id");
	}

	public function lastLocation(): BelongsTo
	{
		return $this->belongsTo(Location::class, "last_location_id", "id");
	}

	public function locations(): BelongsToMany
	{
		return $this->belongsToMany(Location::class, "tblUserLocations");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class);
	}

	public function inventoryTransactions(): HasMany
	{
		return $this->hasMany(InventoryTransaction::class);
	}

	public function clearentTransactions(): HasMany
	{
		return $this->hasMany(ClearentTransaction::class);
	}

	// Return the full name
	public function getFullNameAttribute(): string
	{
		return $this->first_name . " " . $this->last_name;
	}

	public function getIsProviderAttribute(): bool
	{
		return $this->degree !== null && $this->degree !== "";
	}

	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class, "provider_id");
	}

	public function treatmentSheetTreatments(): HasMany
	{
		return $this->hasMany(
			TreatmentSheetTreatment::class,
			"assign_to_user_id",
		);
	}

	public function vaccinationsLastModified(): HasMany
	{
		return $this->hasMany(Vaccination::class, "last_modified_by");
	}

	public function vaccinationsAdministered(): HasMany
	{
		return $this->hasMany(Vaccination::class, "administered_by");
	}
}
