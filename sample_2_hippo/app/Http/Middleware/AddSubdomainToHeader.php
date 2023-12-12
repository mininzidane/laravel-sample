<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Config;

class AddSubdomainToHeader extends Middleware
{
	public function handle($request, Closure $next, ...$guards)
	{
		if (
			$request->hasHeader("referer") &&
			$request->header("referer") !== ""
		) {
			$refererSubdomain = explode(
				".",
				explode("//", $request->header("referer"))[1],
			)[0];

			$request->headers->add(["Subdomain" => $refererSubdomain]);

			$providerName = "auth.providers.subdomain_eloquent_users";
			$providerDetails = Config::get($providerName);
			$providerDetails["subdomain"] = $refererSubdomain;

			Config::set($providerName, $providerDetails);
		} elseif ($request->input("subdomain")) {
			$inputSubdomain = $request->input("subdomain");
			$request->headers->add(["Subdomain" => $inputSubdomain]);
			$providerName = "auth.providers.subdomain_eloquent_users";
			$providerDetails = Config::get($providerName);
			$providerDetails["subdomain"] = $inputSubdomain;

			Config::set($providerName, $providerDetails);
		}

		return $next($request);

		//		if($request->hasCookie('_subdomain')) {
		//			$subdomain = $request->cookie('_subdomain');
		//			$request->headers->add(['Subdomain' => $subdomain]);
		//
		//			$providerName = 'auth.providers.subdomain_eloquent_users';
		//			$providerDetails = Config::get($providerName);
		//			$providerDetails['subdomain'] = $subdomain;
		//
		//			Config::set($providerName, $providerDetails);
		//		} elseif($request->input('subdomain')) {
		//			$request->headers->add(['Subdomain' => $request->input('subdomain')]);
		//		}
		//
		//		return $next($request);
	}
}
