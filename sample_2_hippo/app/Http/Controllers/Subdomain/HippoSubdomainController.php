<?php

namespace App\Http\Controllers\Subdomain;

use App\Extensions\Providers\SubdomainEloquentUserProvider;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HippoSubdomainController extends Controller
{
	/**
	 * Get the guard to be used during authentication.
	 *
	 * @param Request $request
	 * @return StatefulGuard
	 */
	protected function guard(Request $request = null)
	{
		if ($request) {
			Auth::setProvider(
				new SubdomainEloquentUserProvider(
					$request->header("subdomain"),
				),
			);

			return Auth::guard();
		}

		return Auth::guard("api-subdomain-passport");
	}

	protected function addSortCriteria($query, $sortString)
	{
		if ($sortString) {
			$sortColumns = explode(",", $sortString);
			foreach ($sortColumns as $index => $sortColumn) {
				if ($sortColumn === "") {
					continue;
				}

				$sortDetails = explode(":", $sortColumn);

				$query->orderBy($sortDetails[0], $sortDetails[1]);
			}
		}

		return $query;
	}
}
