<?php

namespace App\Models;

use App\GraphQL\Types\OwnerGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Owner
 *
 * @property-read int $id
 *
 * @property int $client_id
 * @property int $organization_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property int $state
 * @property string $zip
 * @property string $country
 * @property string $phone
 * @property string $email
 * @property string $notes
 * @property string $refer
 * @property int $primary
 * @property string $alias_id
 * @property int $removed
 * @property int $timestamp
 * @property string $dob
 * @property string $phone_2
 * @property string $phone_3
 * @property string $dl_number
 * @property string $communication_preference
 * @property int $taxable
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 *
 * @property-read Patient[] $patients
 * @property-read Organization $organization
 * @property-read Sale[] $sales
 * @property-read State $subregion
 * @property-read Credit[] $credits
 * @property-read Payment[] $payments
 * @property-read ClearentToken[] $clearentTokens
 * @property-read Invoice[] $invoices
 *
 * @property-read float $balance
 * @property-read string $full_name
 * @property-read float $accountCreditTotal
 * @property-read array $last_payment
 * @property-read float $amount_due
 */
class Owner extends HippoModel
{
	use SoftDeletes;
	use HasEmailAddress;
	use HasPhoneNumber;
	use HasName, HasFactory;

	public static $graphQLType = OwnerGraphQLType::class;

	protected $table = "tblPatientOwnerInformation";

	protected $fillable = [
		"client_id",
		"organization_id",
		"first_name",
		"middle_name",
		"last_name",
		"address1",
		"address2",
		"city",
		"state",
		"zip",
		"country",
		"phone",
		"email",
		"notes",
		"refer",
		"primary",
		"alias_id",
		"removed",
		"timestamp",
		"dob",
		"phone_2",
		"phone_3",
		"dl_number",
		"communication_preference",
		"taxable",
	];

	protected $appends = [
		"balance",
		"full_name",
		"accountCreditTotal",
		"last_payment",
	];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["first_name", "middle_name", "last_name"];

		parent::__construct($attributes);
	}

	public function patients(): BelongsToMany
	{
		return $this->belongsToMany(
			Patient::class,
			"tblPatientOwners",
			"owner_id",
			"client_id",
		)
			->using(PatientOwner::class)
			->withPivot(["primary", "percent", "relationship_type"])
			->whereNull("tblPatientOwners.deleted_at");
	}

	public function organization(): BelongsTo
	{
		return $this->belongsTo(Organization::class);
	}

	public function sales(): HasMany
	{
		return $this->hasMany(Sale::class, "customer_id", "id");
	}

	public function subregion(): BelongsTo
	{
		return $this->belongsTo(State::class, "state");
	}

	public function credits(): HasMany
	{
		return $this->hasMany(Credit::class);
	}

	public function payments(): HasMany
	{
		return $this->hasMany(Payment::class);
	}

	public function getBalanceAttribute(): float
	{
		$amountDue = $this->getAmountDueAttribute();
		$creditAvailable = $this->getAccountCreditTotalAttribute();

		return $amountDue - $creditAvailable;
	}

	public function getLastPaymentAttribute(): array
	{
		$data = $this->payments()
			->latest("received_at")
			->first();
		return [
			"amount" => $data["amount"] ?? 0,
			"received_at" => $data["received_at"] ?? "",
		];
	}

	public function clearentTokens(): HasMany
	{
		return $this->hasMany(ClearentToken::class);
	}

	public function invoices(): HasMany
	{
		return $this->hasMany(Invoice::class);
	}

	// Return the full name
	public function getFullNameAttribute(): string
	{
		return $this->first_name . " " . $this->last_name;
	}

	public function getAmountDueAttribute(): float
	{
		$amountDue = 0;

		foreach ($this->invoices as $invoice) {
			if ($invoice->status_id === Invoice::OPEN_STATUS) {
				$amountDue += $invoice->getAmountDueAttribute();
			}
		}

		return $amountDue;
	}

	public function getAccountCreditTotalAttribute(): float
	{
		$creditAvailable = 0;

		foreach ($this->credits as $credit) {
			if ($credit->type === Credit::ACCOUNT_CREDIT_TYPE) {
				$creditAvailable += $credit->value;
			}
		}

		return $creditAvailable;
	}
}
