<?php

namespace App\Models;

use App\GraphQL\Types\InvoiceItemTaxGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InvoiceItemTax
 *
 * @property-read int $id
 *
 * @property int $invoice_item_id
 * @property int $tax_id
 * @property string $name
 * @property float $percent
 * @property float $amount
 * @property int $old_sale_item_tax_id
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read InvoiceItem $invoiceItem
 * @property-read Tax $tax
 */
class InvoiceItemTax extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = InvoiceItemTaxGraphQLType::class;

	protected $table = "invoice_item_taxes";

	protected $fillable = [
		"invoice_item_id",
		"tax_id",
		"name",
		"percent",
		"amount",
	];

	public function invoiceItem(): BelongsTo
	{
		return $this->belongsTo(InvoiceItem::class);
	}

	public function tax(): BelongsTo
	{
		return $this->belongsTo(Tax::class);
	}
}
