<?php

namespace App\Http\Controllers;

use App\Models\Orthogonal\ActiveIntegration;

class ActiveIntegrationController extends Controller
{
	public function __construct()
	{
	}

	public function index($type = null)
	{
		$activeIntegrations = ActiveIntegration::where(
			"integration",
			"=",
			$type,
		)->get();

		return response($activeIntegrations);
	}
}
