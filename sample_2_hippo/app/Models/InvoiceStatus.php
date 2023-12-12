<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\GraphQL\Types\InvoiceStatusGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceStatus extends HippoModel
{
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = InvoiceStatusGraphQLType::class;

	protected $table = "invoice_statuses";

	protected $fillable = ["name"];

	const OPEN = 1;
	const COMPLETE = 2;
	const ESTIMATE = 3;
	const VOIDED = 4;

	public function invoices()
	{
		return $this->hasMany(Invoice::class, "status_id", "id");
	}
}
