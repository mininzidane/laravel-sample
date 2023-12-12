<?php

namespace App\Models;

use App\GraphQL\Types\SupplierGraphQLType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $id
 */
class Supplier extends HippoModel
{
	use HasName;
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = SupplierGraphQLType::class;

	protected $table = "suppliers";

	protected $guarded = ["id"];

	protected $fillable = [
		"company_name",
		"account_number",
		"contact_name",
		"email_address",
		"phone_number",
		"address_1",
		"address_2",
		"city",
		"zip_code",
		"state_id",
	];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["company_name"];

		parent::__construct($attributes);
	}

	public function state()
	{
		return $this->belongsTo(State::class);
	}

	public function receivings()
	{
		return $this->hasMany(Receiving::class);
	}

	public function items()
	{
		return $this->hasMany(Item::class, "manufacturer_id", "id");
	}
}
