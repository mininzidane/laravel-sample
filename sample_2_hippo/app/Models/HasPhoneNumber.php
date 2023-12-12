<?php

namespace App\Models;

trait HasPhoneNumber
{
	public $hasPhoneNumber = true;
	protected $phoneNumberFieldName = "phone";

	public function getPhoneNumberFieldName()
	{
		return $this->phoneNumberFieldName;
	}

	public function scopePhoneLike($query, $phoneNumber)
	{
		$phoneSearch =
			"%" .
			str_replace(
				")",
				"",
				str_replace("(", "", str_replace("-", "%", $phoneNumber)),
			) .
			"%";

		$query->where(
			$this->table . "." . $this->phoneNumberFieldName,
			"like",
			$phoneSearch,
		);
	}
}
