<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class HttpsProtocol
{
	public function handle($request, Closure $next)
	{
		if (
			!$request->secure() &&
			!$request->is("status") &&
			App::environment() !== "local"
		) {
			return redirect()->secure($request->getRequestUri());
		}

		return $next($request);
	}
}
