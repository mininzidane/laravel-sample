<?php

namespace App\Models;

use App\GraphQL\Types\InvoiceGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * App\Models\Invoice
 *
 * @property-read int $id
 *
 * @property int $status_id
 * @property int $location_id
 * @property int $patient_id
 * @property int $owner_id
 * @property int $user_id
 * @property bool $active
 * @property string $comment
 * @property bool $print_comment
 * @property float $rounding
 * @property bool $is_taxable
 * @property float $total
 * @property Carbon\Carbon $completed_at
 *
 * @property-read InvoiceStatus $invoiceStatus
 * @property-read Location $location
 * @property-read Patient $patient
 * @property-read Owner $owner
 * @property-read User $user
 * @property-read Collection|InvoicePayment[] $invoicePayments
 * @property-read Collection|InvoiceItem[] $invoiceItems
 * @property-read Collection|InventoryTransaction[] $inventoryTransactions
 * @property-read Collection|TreatmentSheetTreatment[] $treatmentSheetTreatments
 * @property-read Collection|InvoiceAppliedDiscount[] $appliedDiscounts
 * @property-read Collection|Reminder $reminders
 * @property-read Collection|Vaccination $vaccinations
 *
 * @property-read float $totalPayments
 * @property-read float $amountDue
 * @property-read float|null $subtotal
 * @property-read float $taxesTotal
 * @property-read string $type
 * @property-read string $emailMessageType
 * @property-read string|null $emailMessage
 */
class Invoice extends HippoModel
{
	use SoftDeletes, HasFactory;

	const OPEN_STATUS = 1;
	const COMPLETE_STATUS = 2;
	const ESTIMATE_STATUS = 3;

	public static $graphQLType = InvoiceGraphQLType::class;

	protected $table = "invoices";

	protected $primaryKey = "id";

	protected $fillable = [
		"status_id",
		"location_id",
		"patient_id",
		"owner_id",
		"user_id",
		"active",
		"comment",
		"print_comment",
		"rounding",
		"is_taxable",
		"total",
		"completed_at",
		"original_completed_at",
	];

	protected $appends = [
		"totalPayments",
		"amountDue",
		"subtotal",
		"taxesTotal",
		"bulk_payment_id",
		"is_bulk",
	];

	public function invoiceStatus(): BelongsTo
	{
		return $this->belongsTo(InvoiceStatus::class, "status_id", "id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class);
	}

	public function owner(): BelongsTo
	{
		return $this->belongsTo(Owner::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function invoiceItemsWithTrashed()
	{
		return $this->hasMany(InvoiceItem::class)->withTrashed();
	}

	public function invoicePayments(): HasMany
	{
		return $this->hasMany(InvoicePayment::class);
	}

	public function inventoryTransactions(): HasMany
	{
		return $this->hasMany(InventoryTransaction::class);
	}

	public function getTotalPaymentsAttribute(): float
	{
		return array_reduce($this->invoicePayments->toArray(), function (
			$carry,
			$invoicePayment
		) {
			return $carry + $invoicePayment["amount_applied"] * 100;
		}) / 100;
	}

	/**
	 * In order for this to work with GraphQL:
	 * - The total field and the invoice payment amount applied fields MUST be selected
	 *
	 * @return float
	 */
	public function getAmountDueAttribute(): float
	{
		$totalPayments =
			array_reduce($this->invoicePayments->toArray(), function (
				$carry,
				$invoicePayment
			) {
				return $carry + $invoicePayment["amount_applied"] * 100;
			}) / 100;

		return round($this->total - $totalPayments, 2);
	}

	public function getSubtotalAttribute(): ?float
	{
		return array_reduce($this->invoiceItems->toArray(), function (
			$carry,
			$invoiceItem
		) {
			return $carry + $invoiceItem["total"];
		});
	}

	public function getTaxesTotalAttribute(): float
	{
		$taxes = 0;

		foreach ($this->invoiceItems as $invoiceItem) {
			$itemTaxes = array_reduce(
				$invoiceItem->invoiceItemTaxes->toArray(),
				function ($carry, $invoiceItemTax) {
					return $carry + $invoiceItemTax["amount"];
				},
			);

			$taxes += $itemTaxes;
		}

		return $taxes;
	}

	public function getTypeAttribute(): string
	{
		switch ($this->invoiceStatus->id) {
			case self::COMPLETE_STATUS:
				return "Receipt";
			case self::ESTIMATE_STATUS:
				return "Estimate";
			case self::OPEN_STATUS:
			default:
				return "Invoice";
		}
	}

	public function getEmailMessageTypeAttribute(): string
	{
		switch ($this->invoiceStatus->id) {
			case self::COMPLETE_STATUS:
				return "Return Policy";
			case self::ESTIMATE_STATUS:
				return "Estimate Statement";
			case self::OPEN_STATUS:
			default:
				return "Payment Information";
		}
	}

	public function getEmailMessageAttribute(): ?string
	{
		switch ($this->invoiceStatus->id) {
			case self::COMPLETE_STATUS:
				return $this->location->organization->returnPolicy;
			case self::ESTIMATE_STATUS:
				return $this->location->organization->estimateStatement;
			case self::OPEN_STATUS:
			default:
				return $this->location->organization->paymentInfo;
		}
	}

	public function treatmentSheetTreatments(): HasMany
	{
		return $this->hasMany(TreatmentSheetTreatment::class, "invoice_id");
	}

	public function appliedDiscounts(): HasMany
	{
		return $this->hasMany(InvoiceAppliedDiscount::class);
	}

	public function reminders(): HasMany
	{
		return $this->hasMany(Reminder::class);
	}

	public function vaccinations(): HasMany
	{
		return $this->hasMany(Vaccination::class);
	}

	public function payments()
	{
		return $this->belongsToMany(
			Payment::class,
			"invoice_payments",
			"invoice_id",
			"payment_id",
		)->withPivot("amount_applied");
	}

	public function getBulkPaymentIdAttribute()
	{
		$payment = $this->payments()
			->where("payments.is_bulk", 1)
			->first();

		return $payment ? $payment->pivot->payment_id : null;
	}

	public function getIsBulkAttribute()
	{
		$payment = $this->payments()
			->where("payments.is_bulk", 1)
			->first();

		return $payment ? $payment->is_bulk : false;
	}
}
