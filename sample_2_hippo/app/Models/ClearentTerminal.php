<?php

namespace App\Models;

use App\GraphQL\Types\ClearentTerminalGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ClearentTerminal
 *
 * @property int $id
 * @property int $payment_platform_id
 * @property int $location_id
 * @property int $terminal_id
 * @property string $name
 * @property string $api_key
 */
class ClearentTerminal extends HippoModel
{
	use SoftDeletes;
	use HasFactory;
	use HasName;

	protected $nameFields = ["name"];

	public static $graphQLType = ClearentTerminalGraphQLType::class;
	protected $table = "clearent_terminals";

	protected $fillable = [
		"payment_platform_id",
		"location_id",
		"terminal_id",
		"name",
		"api_key",
	];

	public function paymentPlatform()
	{
		return $this->belongsTo(PaymentPlatform::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function clearentTransactions()
	{
		return $this->hasMany(ClearentTransaction::class);
	}
}
