<?php

namespace App\Http\Controllers;

use App\Exceptions\SubdomainNotConfiguredException;
use Config;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * @param $subdomainName
	 * @return void
	 * @throws SubdomainNotConfiguredException
	 *
	 */
	public function createSubdomainConnection($subdomainName)
	{
		$connectionDetails = Config::get("database.connections.hippodb");
		$connectionDetails["database"] = "hippodb_" . $subdomainName;
		$connectionName = "database.connections." . $subdomainName;

		Config::set($connectionName, $connectionDetails);

		try {
			DB::connection($subdomainName)->getPdo();
		} catch (Exception $e) {
			error_log($e);
			throw new SubdomainNotConfiguredException($subdomainName);
		}
	}
}
