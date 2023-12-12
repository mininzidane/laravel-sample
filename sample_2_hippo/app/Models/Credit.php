<?php

namespace App\Models;

use App\GraphQL\Types\CreditGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Credit
 *
 * @property int $id
 * @property string $type
 * @property string $number
 * @property float $original_value
 * @property float $value
 * @property int $owner_id
 * @property int $old_giftcard_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 *
 * @property Owner $owner
 * @property Organization $organization
 * @property Credit $invoice_item
 * @property Payment[] $payments
 */
class Credit extends HippoModel
{
	use HasName, SoftDeletes, HasFactory;

	public static $graphQLType = CreditGraphQLType::class;
	protected $table = "credits";

	protected $fillable = [
		"type",
		"number",
		"original_value",
		"value",
		"owner_id",
	];

	const GIFT_CARD_TYPE = "Gift Card";
	const ACCOUNT_CREDIT_TYPE = "Account Credit";

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["number"];

		parent::__construct($attributes);
	}

	public function owner()
	{
		return $this->belongsTo(Owner::class, "owner_id")->withDefault([
			"id" => null,
			"first_name" => "",
			"middle_name" => "",
			"last_name" => "",
			"full_name" => "",
		]);
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function invoiceItem()
	{
		return $this->hasOne(Credit::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public static function generate_id()
	{
		$data = random_bytes(16);

		$data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
		$data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10

		return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
	}
}
