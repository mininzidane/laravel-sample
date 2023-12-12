<?php

namespace App\Models;

/**
 * App\Models\AntechOrderCode
 *
 * @property int $id
 * @property string $code
 * @property string $description
 * @property float $cost_price
 */
class AntechOrderCode extends HippoModel
{
	protected $table = "tblAntechOrderCodes";

	protected $connection = "hippodb";

	public function antechOrderCode()
	{
		return $this->belongsTo(LabRequisition::class, "order_code_id");
	}
}
