<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AddAuthTokenToHeader extends Middleware
{
	public function handle($request, Closure $next, ...$guards)
	{
		if (
			!$request->bearerToken() &&
			$request->hasHeader("referer") &&
			$request->header("referer") !== ""
		) {
			$refererSubdomain = explode(
				".",
				explode("//", $request->header("referer"))[1],
			)[0];

			if ($request->hasCookie("_subdomain_token_" . $refererSubdomain)) {
				$token = $request->cookie(
					"_subdomain_token_" . $refererSubdomain,
				);
				$request->headers->add(["Authorization" => "Bearer " . $token]);
			}
		}

		return $next($request);
	}
}
