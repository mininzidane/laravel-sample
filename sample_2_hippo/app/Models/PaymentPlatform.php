<?php

namespace App\Models;

use App\GraphQL\Types\PaymentPlatformGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PaymentPlatform
 *
 * @property-read int $id
 *
 * @property string $name
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 */
class PaymentPlatform extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = PaymentPlatformGraphQLType::class;

	protected $table = "payment_platforms";

	protected $fillable = ["name"];

	public function paymentPlatformActivations(): HasMany
	{
		return $this->hasMany(PaymentPlatformActivation::class);
	}

	public function paymentMethods(): HasMany
	{
		return $this->hasMany(PaymentMethod::class);
	}

	public function clearentTerminals(): HasMany
	{
		return $this->hasMany(ClearentTerminal::class);
	}

	public function clearentTransactions(): HasMany
	{
		return $this->hasMany(ClearentTransaction::class);
	}

	public function payments(): HasMany
	{
		return $this->hasMany(Payment::class);
	}
}
