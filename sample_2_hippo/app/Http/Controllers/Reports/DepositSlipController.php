<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DepositSlip;
use Illuminate\Http\Request;

class DepositSlipController extends Controller
{
	/**
	 * @var DepositSlip
	 */
	private $reportModel;

	public function __construct(DepositSlip $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
