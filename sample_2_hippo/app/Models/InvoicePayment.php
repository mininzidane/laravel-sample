<?php

namespace App\Models;

use App\GraphQL\Types\InvoicePaymentGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\InvoicePayment
 *
 * @property-read int $id
 *
 * @property int $invoice_id
 * @property int $payment_id
 * @property float $amount_applied
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 *
 * @property-read Invoice $invoice
 * @property-read Payment $payment
 */
class InvoicePayment extends HippoModel
{
	use HasFactory;

	public static $graphQLType = InvoicePaymentGraphQLType::class;

	protected $table = "invoice_payments";

	protected $fillable = ["invoice_id", "payment_id", "amount_applied"];

	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

	public function payment(): BelongsTo
	{
		return $this->belongsTo(Payment::class);
	}
}
