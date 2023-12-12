<?php

namespace App\Models\Passport;

use Illuminate\Support\Facades\Cookie;
use Laravel\Passport\AuthCode as PassportAuthCode;
use Request;

trait ConnectsToSubdomainIfAvailable
{
	/**
	 * Get the current connection name for the model.
	 *
	 * @return string|null
	 */
	public function getConnectionName()
	{
		$subdomain = request()->header("Subdomain");

		if ($subdomain) {
			$this->connection = $subdomain;
			return $this->connection;
		}

		return config("passport.storage.database.connection") ??
			$this->connection;
	}
}
