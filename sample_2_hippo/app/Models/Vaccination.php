<?php

namespace App\Models;

use App\GraphQL\Types\VaccinationGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $client_id
 * @property int $current_owner_id
 * @property string $current_gender
 * @property float $current_weight
 * @property int $item_kit_id
 * @property int $reminder_id
 * @property int $reminder_item_id
 * @property int $vaccine_item_id
 * @property string $vaccine_name
 * @property float $dosage
 * @property string $receiving_item_lot_number
 * @property \DateTimeInterface $receiving_item_expiration_date
 * @property string $serialnumber
 * @property \DateTimeInterface $timestamp
 * @property int $seen_by
 * @property bool $removed
 * @property \DateTimeInterface $administered_date
 * @property int $location_administered
 * @property \DateTimeInterface $last_modified
 * @property int $last_modified_user
 * @property int $administered_by
 * @property int $manufacturer_supplier
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @property-read Patient $patient
 * @property-read Item $item
 * @property-read Invoice $invoice
 * @property-read InvoiceItem $invoiceItem
 * @property-read Location $locationAdministered
 * @property-read User $lastModifiedBy
 * @property-read User $administeredBy
 * @property-read User $provider
 * @property-read Item $itemKit
 * @mixin \Eloquent
 */
class Vaccination extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = VaccinationGraphQLType::class;

	protected $table = "tblPatientVaccines";

	protected $fillable = [
		"client_id",
		"current_owner_id",
		"current_gender",
		"current_weight",
		"item_kit_id",
		"reminder_id",
		"reminder_item_id",
		"vaccine_item_id",
		"vaccine_name",
		"dosage",
		"receiving_item_lot_number",
		"receiving_item_expiration_date",
		"serialnumber",
		"timestamp",
		"seen_by",
		"removed",
		"administered_date",
		"location_administered",
		"last_modified",
		"last_modified_user",
		"administered_by",
		"manufacturer_supplier",
	];

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, "vaccine_item_id");
	}

	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class, "invoice_id");
	}

	public function invoiceItem(): BelongsTo
	{
		return $this->belongsTo(InvoiceItem::class, "invoice_item_id");
	}

	public function locationAdministered(): BelongsTo
	{
		return $this->belongsTo(Location::class, "location_administered");
	}

	public function lastModifiedBy(): BelongsTo
	{
		return $this->belongsTo(User::class, "last_modified_user");
	}

	public function administeredBy(): BelongsTo
	{
		return $this->belongsTo(User::class, "administered_by");
	}

	public function provider(): BelongsTo
	{
		return $this->belongsTo(User::class, "seen_by");
	}

	public function itemKit(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function getFormattedPatientsAgeAttribute(): string
	{
		if (
			$this->patient->date_of_birth === "0000-00-00" ||
			is_null($this->patient->date_of_birth)
		) {
			return "N/A";
		}

		$patientBirthDate = new Carbon($this->patient->date_of_birth);
		$vaccineAdminDate = new Carbon($this->administered_date);

		$ageAtVaccine = $patientBirthDate->diff($vaccineAdminDate);

		if ($ageAtVaccine->y >= 1) {
			return $ageAtVaccine->y .
				" " .
				Str::plural("year", $ageAtVaccine->y) .
				" " .
				$ageAtVaccine->m .
				" " .
				Str::plural("month", $ageAtVaccine->m);
		}

		$weeks = $ageAtVaccine->days / 7;

		if ($weeks > 16) {
			return $ageAtVaccine->m .
				" " .
				Str::plural("month", $ageAtVaccine->m);
		}

		$remainingDays = $ageAtVaccine->days % 7;

		return floor($weeks) .
			" " .
			Str::plural("week", $weeks) .
			" " .
			$remainingDays .
			" " .
			Str::plural("day", $remainingDays);
	}
}
