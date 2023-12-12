<?php

namespace App\Http\Controllers;

use App\Extensions\SHA1Hasher;
use App\Extensions\Providers\SubdomainEloquentUserProvider;
use App\Models\AntechOrderCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class LabOrderCodeController extends Controller
{
	public function index()
	{
		//
	}

	public function save(Request $request, $type = "ANTECH")
	{
		$code = $request->get("code");
		$description = $request->get("description");
		$costPrice = $request->get("costPrice");

		$orderCodeClass = AntechOrderCode::class;

		if (strtoupper($type) == "ANTECH") {
			$orderCodeClass = AntechOrderCode::class;
		}

		$orderCode = new $orderCodeClass();

		$orderCode->updateOrCreate(
			["code" => $code],
			["description" => $description, "cost_price" => $costPrice],
		);
	}

	public function testings()
	{
	}
}
