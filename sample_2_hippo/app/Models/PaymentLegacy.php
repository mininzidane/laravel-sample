<?php

namespace App\Models;

use App\GraphQL\Types\PaymentLegacyGraphQLType;

class PaymentLegacy extends HippoModel
{
	public static $graphQLType = PaymentLegacyGraphQLType::class;

	protected $table = "ospos_sales_payments";

	protected $primaryKey = "payment_id";

	protected $fillable = [
		"payment_id",
		"sale_id",
		"payment_type",
		"payment_amount",
		"organization_id",
		"timestamp",
		"offset",
		"paymentstripeToken",
		"bulk_payment_id",
		"payment_date",
		"stripe_processed",
		"payment_transaction_id",
	];

	public function sale()
	{
		return $this->belongsTo(Sale::class, "sale_id", "sale_id");
	}
}
