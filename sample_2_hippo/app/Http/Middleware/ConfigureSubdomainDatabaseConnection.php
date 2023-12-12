<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Config;

class ConfigureSubdomainDatabaseConnection extends Middleware
{
	public function handle($request, Closure $next, ...$guards)
	{
		$subdomain = $request->header("Subdomain");

		if ($subdomain) {
			$connectionDetails = Config::get("database.connections.hippodb");
			$connectionDetails["database"] = "hippodb_" . $subdomain;
			$connectionName = "database.connections." . $subdomain;

			Config::set($connectionName, $connectionDetails);

			$connectionDetails = Config::get("database.connections.replica");
			$connectionDetails["database"] = "hippodb_" . $subdomain;
			$connectionName = "database.connections.replica_" . $subdomain;

			Config::set($connectionName, $connectionDetails);
		}

		return $next($request);
	}
}
