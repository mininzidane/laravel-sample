<?php

namespace App\Models;

class ZoetisOrderCode extends HippoModel
{
	protected $table = "tblZoetisOrderCodes";

	protected $connection = "hippodb";

	protected $fillable = [
		"description",
		"code",
		"replicate",
		"validFrom",
		"includes",
		"currency",
		"non_discountable",
		"type",
	];

	public function zoetisOrderCode()
	{
		return $this->belongsTo(LabRequisition::class, "order_code_id");
	}
}
