<?php

namespace App\Models;

use App\GraphQL\Types\PaymentMethodGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property bool $protected
 * @property bool $active
 * @property bool $user_facing
 * @property bool $is_depositable
 * @property string $process_type
 * @property int $payment_platform_id
 *
 * @property-read PaymentPlatform $paymentPlatform
 * @property-read Payment[] $payments
 * @mixin \Eloquent
 */
class PaymentMethod extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public const PROCESS_TYPE_STANDARD = "STANDARD";
	public const PROCESS_TYPE_CREDITS = "CREDITS";
	public const PROCESS_TYPE_PAYMENT_PLATFORM = "PAYMENT_PLATFORM";
	public const PROCESS_TYPE_ISSUE_CREDIT = "ISSUE_CREDIT";
	public const PROCESS_TYPE_GIFT_CARD = "GIFT_CARD";
	public const PROCESS_TYPE_DISPENSE_CHANGE = "DISPENSE_CHANGE";
	public const PROCESS_TYPES = [
		self::PROCESS_TYPE_STANDARD,
		self::PROCESS_TYPE_CREDITS,
		self::PROCESS_TYPE_PAYMENT_PLATFORM,
		self::PROCESS_TYPE_ISSUE_CREDIT,
		self::PROCESS_TYPE_GIFT_CARD,
		self::PROCESS_TYPE_DISPENSE_CHANGE,
	];

	public static $graphQLType = PaymentMethodGraphQLType::class;

	protected $table = "payment_methods";

	protected $fillable = [
		"name",
		"protected",
		"active",
		"user_facing",
		"is_depositable",
		"process_type",
		"payment_platform_id",
	];

	public function paymentPlatform(): BelongsTo
	{
		return $this->belongsTo(
			PaymentPlatform::class,
			"payment_platform_id",
			"id",
		);
	}

	public function payments(): HasMany
	{
		return $this->hasMany(Payment::class);
	}
}
