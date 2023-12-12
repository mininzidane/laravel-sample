<?php

namespace App\Models;

use App\GraphQL\Types\SaleStatusGraphQLType;

/**
 * @property int $status_id
 * @property string $status_name
 *
 * @mixin \Eloquent
 */
class SaleStatus extends HippoModel
{
	public static $graphQLType = SaleStatusGraphQLType::class;

	public $timestamps = false;

	protected $table = "tblSalesStatuses";

	protected $primaryKey = "status_id";

	protected $fillable = ["status_name"];

	public function getStatusNameAttribute($value)
	{
		return strtoupper($value);
	}
}
