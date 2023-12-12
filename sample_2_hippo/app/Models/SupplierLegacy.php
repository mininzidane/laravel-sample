<?php

namespace App\Models;

use App\GraphQL\Types\SupplierLegacyGraphQLType;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierLegacy extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;

	public static $graphQLType = SupplierLegacyGraphQLType::class;

	protected $table = "ospos_suppliers";

	protected $fillable = [
		"person_id",
		"company_name",
		"account_number",
		"deleted",
		"organization_id",
	];

	public function user()
	{
		return $this->belongsTo(User::class, "person_id");
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function receivings()
	{
		return $this->hasMany(ReceivingLegacy::class, "supplier_id");
	}
}
