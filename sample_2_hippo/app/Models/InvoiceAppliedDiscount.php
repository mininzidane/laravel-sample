<?php

namespace App\Models;

use App\GraphQL\Types\InvoiceAppliedDiscountGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InvoiceAppliedDiscount
 *
 * @property-read int $id
 *
 * @property int $invoice_id
 * @property int $discount_invoice_item_id
 * @property int $adjusted_invoice_item_id
 * @property float $amount_applied
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Invoice $invoice
 * @property-read InvoiceItem $discountInvoiceItem
 * @property-read InvoiceItem $discountApplications
 */
class InvoiceAppliedDiscount extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = InvoiceAppliedDiscountGraphQLType::class;

	protected $table = "invoice_applied_discounts";

	protected $fillable = [
		"invoice_id",
		"discount_invoice_item_id",
		"adjusted_invoice_item_id",
		"amount_applied",
	];

	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

	public function discountInvoiceItem(): BelongsTo
	{
		return $this->belongsTo(InvoiceItem::class);
	}

	public function discountApplications(): BelongsTo
	{
		return $this->belongsTo(InvoiceItem::class);
	}
}
