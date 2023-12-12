<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ZendController extends Controller
{
	public function zendAuth()
	{
		$user = Auth::guard("api-subdomain-passport")->user();

		return response()->json([$user]);
	}
}
