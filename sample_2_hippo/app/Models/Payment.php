<?php

namespace App\Models;

use App\GraphQL\Types\PaymentGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $owner_id
 * @property int $payment_method_id
 * @property float $amount
 * @property string $received_at
 * @property int $payment_platform_id
 * @property int $clearent_transaction_id
 * @property int $credit_id
 *
 * @property-read Owner $owner
 * @property-read PaymentMethod $paymentMethod
 * @property-read Credit $credit
 * @property-read PaymentPlatform $paymentPlatform
 * @property-read ClearentTransaction $clearentTransaction
 * @property-read Invoice[] $invoices
 * @mixin \Eloquent
 */
class Payment extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = PaymentGraphQLType::class;

	protected $table = "payments";

	protected $fillable = [
		"owner_id",
		"payment_method_id",
		"amount",
		"received_at",
		"payment_platform_id",
		"clearent_transaction_id",
		"credit_id",
		"is_bulk",
	];

	public function owner(): BelongsTo
	{
		return $this->belongsTo(Owner::class);
	}

	public function paymentMethod(): BelongsTo
	{
		return $this->belongsTo(PaymentMethod::class);
	}

	public function credit(): BelongsTo
	{
		return $this->belongsTO(Credit::class);
	}

	public function paymentPlatform(): BelongsTo
	{
		return $this->belongsTo(PaymentPlatform::class);
	}

	public function clearentTransaction(): HasOne
	{
		return $this->hasOne(ClearentTransaction::class);
	}

	public function invoices(): BelongsToMany
	{
		return $this->belongsToMany(
			Invoice::class,
			"invoice_payments",
		)->withTimestamps();
	}
}
