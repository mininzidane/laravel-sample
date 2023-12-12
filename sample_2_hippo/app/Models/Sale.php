<?php

namespace App\Models;

use App\GraphQL\Types\SaleGraphQLType;

class Sale extends HippoModel
{
	use IsDated;

	public static $graphQLType = SaleGraphQLType::class;

	protected $table = "ospos_sales";

	protected $primaryKey = "sale_id";

	protected $fillable = [
		"sale_time",
		"customer_id",
		"employee_id",
		"comment",
		"comment_print_check",
		"payment_type",
		"organization_id",
		"location_id",
		"updated_total",
		"client_id",
		"note_type",
		"note_id",
		"sale_completed_time",
		"rounding",
		"updated_time",
		"sale_status",
		"taxable",
	];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$this->primaryDateField = "sale_time";
	}

	public function lineItems()
	{
		return $this->hasMany(LineItem::class, "sale_id", "sale_id");
	}

	public function items()
	{
		return $this->belongsToMany(
			ItemLegacy::class,
			"ospos_sales_items",
			"sale_id",
			"item_id",
		)->using(LineItemPivot::class);
	}

	public function owner()
	{
		return $this->belongsTo(Owner::class, "customer_id", "id");
	}

	public function patient()
	{
		return $this->belongsTo(Patient::class, "client_id", "id");
	}

	public function employee()
	{
		return $this->belongsTo(User::class, "employee_id", "id");
	}

	public function reminders()
	{
		return $this->hasMany(Reminder::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function payments()
	{
		return $this->hasMany(PaymentLegacy::class, "sale_id");
	}

	public function status()
	{
		return $this->belongsTo(SaleStatus::class, "sale_status", "status_id");
	}

	public function dispensations()
	{
		return $this->hasMany(Dispensation::class, "sale_id");
	}

	//	public function getSaleStatusAttribute($value)
	//	{
	//		switch($value) {
	//			case 1:
	//			default:
	//				return 'OPEN';
	//			case 2:
	//				return 'COMPLETE';
	//			case 3:
	//				return 'ESTIMATE';
	//		}
	//	}
}
