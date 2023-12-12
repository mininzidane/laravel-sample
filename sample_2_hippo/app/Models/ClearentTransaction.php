<?php

namespace App\Models;

use App\GraphQL\Types\ClearentTransactionGraphQLType;

class ClearentTransaction extends HippoModel
{
	public static $graphQLType = ClearentTransactionGraphQLType::class;

	protected $table = "clearent_transactions";

	protected $fillable = [
		"payment_platform_id",
		"clearent_terminal_id",
		"terminal_id",
		"user_id",
		"token_id",
		"request_id",
		"request_type",
		"response_status",
		"card_type",
		"last_four_digits",
		"authorization_code",
		"request_body",
		"response_body",
		"platform_mode",
		"payment_id",
	];

	public function paymentPlatform()
	{
		return $this->belongsTo(PaymentPlatform::class);
	}

	public function clearentTerminal()
	{
		return $this->belongsTo(ClearentTerminal::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function payment()
	{
		return $this->belongsTo(Payment::class);
	}

	public function token()
	{
		return $this->hasOne(
			ClearentToken::class,
			"origin_transaction_id",
			"id",
		);
	}
}
