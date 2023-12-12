<?php

namespace App\Models;

use App\GraphQL\Types\PaymentPlatformActivationGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PaymentPlatformActivation
 *
 * @property-read int $id
 *
 * @property int $payment_platform_id
 * @property int $location_id
 * @property string $mode
 * @property string $info
 * @property bool $is_active
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 *
 * @property-read PaymentPlatform $paymentPlatform
 * @property-read Location $location
 */
class PaymentPlatformActivation extends HippoModel
{
	use HasFactory;

	public static $graphQLType = PaymentPlatformActivationGraphQLType::class;

	protected $table = "payment_platform_activations";

	protected $fillable = [
		"payment_platform_id",
		"location_id",
		"mode",
		"info",
		"is_active",
	];

	public function paymentPlatform(): BelongsTo
	{
		return $this->belongsTo(PaymentPlatform::class);
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}
}
