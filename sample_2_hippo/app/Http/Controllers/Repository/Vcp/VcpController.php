<?php

namespace App\Http\Controllers\Repository\Vcp;

use App\Http\Controllers\Controller;
use App\Repositories\Vcp\VcpRepository;
use Illuminate\Http\Request;

class VcpController extends Controller
{
	public function vcpContractReportPdf(Request $request)
	{
		$subdomain = $request->header("subdomain");
		$id = $request->input("id");

		$reportModel = new VcpRepository();
		return $reportModel->getClientContractPdf($subdomain, $id);
	}
}
