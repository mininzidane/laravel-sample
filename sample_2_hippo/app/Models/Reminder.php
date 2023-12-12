<?php

namespace App\Models;

use App\GraphQL\Types\ReminderGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $organization_id
 * @property int $location_id
 * @property int $client_id
 * @property int $item_id
 * @property int $sale_id
 * @property int $invoice_id
 * @property int $invoice_item_id
 * @property string $description
 * @property string $frequency
 * @property \DateTimeInterface $start_date
 * @property \DateTimeInterface $due_date
 * @property \DateTimeInterface $email_sent
 * @property int $removed_by_item_id
 * @property \DateTimeInterface $removed_datetime
 * @property bool $removed
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @property-read Patient $patient
 * @property-read Location $location
 * @property-read Invoice $invoice
 * @property-read InvoiceItem $invoiceItem
 * @property-read Item $item
 * @property-read Sale $sale
 * @mixin \Eloquent
 */
class Reminder extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ReminderGraphQLType::class;

	protected $table = "tblClientReminders";

	protected $fillable = [
		"organization_id",
		"location_id",
		"client_id",
		"item_id",
		"sale_id",
		"invoice_id",
		"invoice_item_id",
		"description",
		"frequency",
		"start_date",
		"due_date",
		"email_sent",
		"removed_by_item_id",
		"removed_datetime",
		"removed",
	];

	public function patient(): BelongsTo
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}

	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

	public function invoiceItem(): BelongsTo
	{
		return $this->belongsTo(InvoiceItem::class, "invoice_item_id");
	}

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class, "item_id");
	}

	public function sale(): BelongsTo
	{
		return $this->belongsTo(Sale::class, "sale_id");
	}
}
