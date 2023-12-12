<?php

namespace App\Models;

trait HasEmailAddress
{
	public $hasEmailAddress = true;
	protected $emailAddressFieldName = "email";

	public function getEmailAddressFieldName()
	{
		return $this->emailAddressFieldName;
	}

	public function scopeEmailLike($query, $emailAddressComponent)
	{
		$query->where(
			$this->table . "." . $this->emailAddressFieldName,
			"like",
			"%" . $emailAddressComponent . "%",
		);
	}
}
