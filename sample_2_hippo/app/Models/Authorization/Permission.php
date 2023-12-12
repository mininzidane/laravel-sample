<?php

namespace App\Models\Authorization;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
	public function __construct(array $attributes = [])
	{
		if (request()->header("Subdomain")) {
			$this->setConnection(request()->header("Subdomain"));
		}

		parent::__construct($attributes);
	}

	public function subdomain()
	{
		return $this->hasOne(Subdomain::class);
	}
}
