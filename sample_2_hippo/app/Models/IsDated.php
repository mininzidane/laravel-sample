<?php

namespace App\Models;

trait IsDated
{
	public $primaryDateField = "updated_at";

	public function getPrimaryDateKey()
	{
		return $this->primaryDateField;
	}

	public function scopeAfter($query, $date)
	{
		$query->where($this->table . "." . $this->primaryDateField, ">", $date);
	}

	public function scopeBefore($query, $date)
	{
		$query->where($this->table . "." . $this->primaryDateField, "<", $date);
	}

	public function scopeBetween($query, $startDate, $endDate)
	{
		$query->whereBetween($this->table . "." . $this->primaryDateField, [
			$startDate,
			$endDate,
		]);
	}
}
