<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZoetisOrderCode;

class ZoetisLabOrderCodeController extends Controller
{
	public function save(
		Request $request,
		ZoetisOrderCode $zoetisOrderCodeClass
	) {
		$zoetisOrderCodeClass->updateOrCreate(
			["description" => $request->get("description")],
			[
				"code" => $request->get("code"),
				"replicate" => $request->get("replicate"),
				"validFrom" => $request->get("validFrom"),
				"includes" => $request->get("includes"),
				"currency" => $request->get("currency"),
				"non_discountable" => $request->get("nonDiscountable"),
				"type" => $request->get("type"),
			],
		);
	}
}
