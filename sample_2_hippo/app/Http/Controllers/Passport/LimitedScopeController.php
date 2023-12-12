<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Support\Collection;
use Laravel\Passport\Passport;

class LimitedScopeController
{
	/**
	 * Get all of the available scopes for the application.
	 *
	 * @return Collection
	 */
	public function all()
	{
		return Passport::scopes();
	}
}
