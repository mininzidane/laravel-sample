<?php

namespace App\Models\Authorization;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
	public function __construct(array $attributes = [])
	{
		if (request()->header("Subdomain")) {
			$this->setConnection(request()->header("Subdomain"));
		}

		parent::__construct($attributes);
	}
}
