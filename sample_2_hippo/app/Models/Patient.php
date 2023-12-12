<?php

namespace App\Models;

use App\GraphQL\Types\PatientGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\PatientImage
 *
 * @property int $organization_id
 * @property int $id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $prefix
 * @property string $suffix
 * @property \Carbon\Carbon $date_of_birth
 * @property \Carbon\Carbon $date_of_death
 * @property string $gender_id
 * @property string $species
 * @property string $breed
 * @property string $marking
 * @property string $color
 * @property string $ethnicity
 * @property string $race
 * @property string $ssn
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $county
 * @property string $timezone
 * @property string $drivers_license_state
 * @property string $drivers_license_number
 * @property string $home_phone
 * @property string $work_phone
 * @property string $work_phone_ext
 * @property string $cell_phone
 * @property string $fax
 * @property string $prefered_communication
 * @property string $email
 * @property string $language
 * @property bool $phr
 * @property string $notes
 * @property int $added_by
 * @property string $license
 * @property string $microchip
 * @property string $alias_id
 * @property string $vcp_contract_id
 * @property bool $removed
 * @property \Carbon\Carbon $timestamp
 * @property \Carbon\Carbon $history_last_modified
 * @property int $history_last_modified_by
 * @property string $image_type
 * @property string $size
 */

class Patient extends HippoModel
{
	use SoftDeletes;
	use HasName, HasFactory;

	public static $graphQLType = PatientGraphQLType::class;

	protected $table = "tblClients";

	protected $fillable = [
		"organization_id",
		"first_name",
		"middle_name",
		"last_name",
		"prefix",
		"suffix",
		"date_of_birth",
		"date_of_death",
		"gender_id",
		"species",
		"breed",
		"marking",
		"color",
		"ethnicity",
		"race",
		"ssn",
		"address1",
		"address2",
		"address3",
		"city",
		"state",
		"zip",
		"county",
		"timezone",
		"drivers_license_state",
		"drivers_license_number",
		"home_phone",
		"work_phone",
		"work_phone_ext",
		"cell_phone",
		"fax",
		"prefered_communication",
		"email",
		"language",
		"phr",
		"notes",
		"added_by",
		"license",
		"microchip",
		"alias_id",
		"vcp_contract_id",
		"removed",
		"timestamp",
		"history_last_modified",
		"history_last_modified_by",
	];

	protected $appends = [
		"currentWeight",
		"lastVet",
		"rabies",
		"primaryImage",
		"lastVisit",
		"primaryOwner",
		"formattedAge",
	];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["first_name"];

		parent::__construct($attributes);
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class);
	}

	public function owners()
	{
		return $this->belongsToMany(
			Owner::class,
			"tblPatientOwners",
			"client_id",
			"owner_id",
		)
			->using(PatientOwner::class)
			->withPivot(["primary", "percent", "relationship_type"])
			->wherePivotNull("deleted_at");
	}

	public function preferredLocation()
	{
		return $this->belongsTo(Location::class);
	}

	public function providers()
	{
		return $this->belongsToMany(
			Owner::class,
			"tblPatientOwners",
			"client_id",
			"owner_id",
		)
			->using(PatientOwner::class)
			->withPivot(["primary", "percent", "relationship_type"])
			->where("tblPatientOwners.relationship_type", "=", "Veterinarian")
			->wherePivotNull("deleted_at");
	}

	public function getPrimaryOwnerAttribute()
	{
		foreach ($this->owners as $owner) {
			if ($owner->pivot->primary) {
				return $owner;
			}
		}
	}

	public function reminders()
	{
		return $this->hasMany(Reminder::class, "client_id");
	}

	public function sales()
	{
		return $this->hasMany(Sale::class, "client_id", "id");
	}

	public function appointments()
	{
		return $this->hasMany(Appointment::class, "client_id");
	}

	public function lineItems()
	{
		return $this->hasMany(LineItem::class, "client_id");
	}

	public function speciesRelation()
	{
		return $this->belongsTo(Species::class, "species_id");
	}

	public function soapCharts()
	{
		return $this->hasMany(SoapChart::class, "client_id");
	}

	public function historyCharts()
	{
		return $this->hasMany(HistoryChart::class, "client_id");
	}

	public function treatmentCharts()
	{
		return $this->hasMany(TreatmentChart::class, "client_id");
	}

	public function phoneCharts()
	{
		return $this->hasMany(PhoneChart::class, "client_id");
	}

	public function emailCharts()
	{
		return $this->hasMany(EmailChart::class, "client_id");
	}

	public function progressCharts()
	{
		return $this->hasMany(ProgressChart::class, "client_id");
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class, "prescription_id");
	}

	public function gender_relation()
	{
		return $this->belongsTo(Gender::class, "gender_id");
	}

	public function vaccinations()
	{
		return $this->hasMany(Vaccination::class, "client_id");
	}

	public function images()
	{
		return $this->hasMany(PatientImage::class, "client_id")->latest();
	}

	public function getPrimaryImageAttribute(): string
	{
		return $this->images()
			->latest()
			->first()->presignedUrl ?? "img/hippo-avatar.svg";
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function activeInvoice()
	{
		return $this->hasOne(Invoice::class);
	}

	public function getBreedAttribute()
	{
		$connection = $this->getConnectionName();

		$breeds = DB::connection($connection)
			->table("tblClients")
			->where("tblClients.id", "=", $this->id)
			->join(
				"tblPatientAnimalBreeds",
				"tblClients.id",
				"=",
				"tblPatientAnimalBreeds.client_id",
			)
			->join(
				"tblBreeds",
				function ($join) {
					$join
						->on(
							"tblBreeds.name",
							"=",
							"tblPatientAnimalBreeds.breed",
						)
						->on("tblBreeds.species", "=", "tblClients.species");
				},
				"=",
				null,
				"left outer",
			)
			->get();

		$breedNames = [];

		foreach ($breeds as $breed) {
			array_push($breedNames, $breed->breed);
		}

		return implode(", ", $breedNames);
	}

	public function getColorAttribute()
	{
		$connection = $this->getConnectionName();

		$colors = DB::connection($connection)
			->table("tblClients")
			->where("tblClients.id", "=", $this->id)
			->join(
				"tblPatientAnimalColors",
				"tblClients.id",
				"=",
				"tblPatientAnimalColors.client_id",
			)
			->join(
				"tblColors",
				function ($join) {
					$join
						->on(
							"tblColors.name",
							"=",
							"tblPatientAnimalColors.color",
						)
						->on("tblColors.species", "=", "tblClients.species");
				},
				"=",
				null,
				"left outer",
			)
			->get();

		$colorNames = [];

		foreach ($colors as $color) {
			array_push($colorNames, $color->color);
		}

		return implode(", ", $colorNames);
	}

	public function getCurrentWeightAttribute()
	{
		$connection = $this->getConnectionName();

		$soapChartQuery = SoapChart::on($connection)
			->where("client_id", "=", $this->id)
			->whereNotNull("vs_wt");
		$historyChartQuery = HistoryChart::on($connection)
			->where("client_id", "=", $this->id)
			->whereNotNull("vs_wt");
		$phoneChartQuery = PhoneChart::on($connection)
			->where("client_id", "=", $this->id)
			->whereNotNull("vs_wt");
		$treatmentChartQuery = TreatmentChart::on($connection)
			->where("client_id", "=", $this->id)
			->whereNotNull("vs_wt");
		$emailChartQuery = EmailChart::on($connection)
			->where("client_id", "=", $this->id)
			->whereNotNull("vs_wt");
		$progressChartQuery = ProgressChart::on($connection)
			->where("client_id", "=", $this->id)
			->whereNotNull("vs_wt");

		$unionQuery = $soapChartQuery
			->union($historyChartQuery)
			->union($phoneChartQuery)
			->union($treatmentChartQuery)
			->union($emailChartQuery)
			->union($progressChartQuery);

		$currentWeight = $unionQuery->orderBy("date", "desc")->pluck("vs_wt");

		if ($currentWeight && isset($currentWeight[0])) {
			return $currentWeight[0];
		}

		return null;
	}

	public function getLastVetAttribute()
	{
		$connection = $this->getConnectionName();

		$soapChartQuery = SoapChart::on($connection)->where(
			"client_id",
			"=",
			$this->id,
		);
		$historyChartQuery = HistoryChart::on($connection)->where(
			"client_id",
			"=",
			$this->id,
		);
		$phoneChartQuery = PhoneChart::on($connection)->where(
			"client_id",
			"=",
			$this->id,
		);
		$treatmentChartQuery = TreatmentChart::on($connection)->where(
			"client_id",
			"=",
			$this->id,
		);
		$emailChartQuery = EmailChart::on($connection)->where(
			"client_id",
			"=",
			$this->id,
		);
		$progressChartQuery = ProgressChart::on($connection)->where(
			"client_id",
			"=",
			$this->id,
		);

		$unionQuery = $soapChartQuery
			->union($historyChartQuery)
			->union($phoneChartQuery)
			->union($treatmentChartQuery)
			->union($emailChartQuery)
			->union($progressChartQuery);

		$chart = $unionQuery->orderBy("updated_at", "desc")->first();

		if (!$chart) {
			return null;
		}

		if (!$chart->seen_by) {
			return null;
		}

		try {
			$vet = User::on($connection)->findOrFail($chart->seen_by);
			$vetName = $vet->first_name . " " . $vet->last_name;
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
			$vetName = null;
		}

		return $vetName;
	}

	public function getRabiesAttribute()
	{
		$connection = $this->getConnectionName();

		$vaccinations = Vaccination::on($connection)
			->with("item")
			->where("client_id", "=", $this->id)
			->get();

		foreach ($vaccinations as $vaccination) {
			if ($vaccination->item && $vaccination->item->is_serialized) {
				return true;
			}
		}

		return false;
	}

	public function patientAlerts()
	{
		return $this->hasMany(PatientAlert::class, "client_id");
	}

	public function patientAllergy()
	{
		return $this->hasMany(PatientAllergy::class, "client_id")->where(
			"removed",
			"=",
			0,
		);
	}

	public function patientDrugAllergy()
	{
		return $this->hasMany(PatientDrugAllergy::class, "client_id")->where(
			"removed",
			"=",
			0,
		);
	}

	public function patientAllergyNote()
	{
		return $this->hasMany(PatientAllergyNote::class, "client_id");
	}

	public function treatmentSheetTreatments()
	{
		return $this->hasMany(TreatmentSheetTreatment::class, "client_id");
	}

	public function getDeceasedAttribute()
	{
		return $this->date_of_death !== null &&
			$this->date_of_death !== "0000-00-00";
	}

	public function getLastVisitAttribute()
	{
		$connection = $this->getConnectionName();

		$lastVisit = DB::connection($connection)
			->table("tblSchedule")
			->where("tblSchedule.client_id", "=", $this->id)
			->join(
				"tblAppointmentStatuses",
				"tblSchedule.status",
				"=",
				"tblAppointmentStatuses.status_key",
			)
			->where("tblAppointmentStatuses.last_visit_status", "=", "1")
			->orderBy("start_time", "desc")
			->limit(1)
			->first();

		return $lastVisit->start_time ?? "";
	}

	public function getNameAttribute()
	{
		if (sizeof($this->nameFields) === 0) {
			return null;
		}

		$names = [];

		foreach ($this->nameFields as $nameField) {
			if ($this[$nameField]) {
				$names[] = $this[$nameField];
			}
		}

		return implode(" ", $names);
	}

	public function getAmountDueAttribute()
	{
		$amountDue = 0;

		foreach ($this->invoices as $invoice) {
			if ($invoice->status_id === Invoice::OPEN_STATUS) {
				$amountDue += $invoice->getAmountDueAttribute();
			}
		}

		return $amountDue;
	}

	public function getFormattedAgeAttribute()
	{
		if (
			$this->date_of_death !== "0000-00-00" &&
			!is_null($this->date_of_death)
		) {
			return "DECEASED";
		}

		if (
			$this->date_of_birth === "0000-00-00" ||
			is_null($this->date_of_birth)
		) {
			return "N/A";
		}

		$patientAge = (new Carbon($this->date_of_birth))->diff(now());

		if ($patientAge->y >= 1) {
			return $patientAge->y .
				" " .
				Str::plural("year", $patientAge->y) .
				" " .
				$patientAge->m .
				" " .
				Str::plural("month", $patientAge->m);
		}

		$weeks = $patientAge->days / 7;

		if ($weeks > 16) {
			return $patientAge->m . " " . Str::plural("month", $patientAge->m);
		}

		$remainingDays = $patientAge->days % 7;

		return floor($weeks) .
			" " .
			Str::plural("week", $weeks) .
			" " .
			$remainingDays .
			" " .
			Str::plural("day", $remainingDays);
	}
}
