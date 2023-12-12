<?php

namespace App\Models;

use App\GraphQL\Types\ClearentTokenGraphQLType;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClearentToken extends HippoModel
{
	use SoftDeletes;

	public static $graphQLType = ClearentTokenGraphQLType::class;

	protected $table = "clearent_tokens";

	protected $fillable = [
		"card_token",
		"name",
		"owner_id",
		"origin_transaction_id",
		"card_type",
		"last_four_digits",
		"expiration_date",
	];

	public function owner()
	{
		return $this->belongsTo(Owner::class);
	}

	public function clearentTransaction()
	{
		return $this->belongsTo(
			ClearentTransaction::class,
			"origin_transaction_id",
			"id",
		);
	}
}
