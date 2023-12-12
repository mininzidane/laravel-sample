<?php

namespace App\Models;

use App\GraphQL\Types\ReceivingLegacyGraphQLType;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivingLegacy extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;

	public static $graphQLType = ReceivingLegacyGraphQLType::class;

	protected $table = "ospos_receivings";

	protected $primaryKey = "receiving_id";

	protected $fillable = [
		"receiving_time",
		"supplier_id",
		"employee_id",
		"comment",
		"payment_type",
		"organization_id",
		"location_id",
	];

	public function user()
	{
		return $this->belongsTo(User::class, "employee_id");
	}

	public function location()
	{
		return $this->belongsTo(Location::class, "location_id");
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function receivingItems()
	{
		return $this->hasMany(ReceivingItemLegacy::class, "receiving_id");
	}

	public function supplier()
	{
		return $this->belongsTo(SupplierLegacy::class, "supplier_id");
	}
}
