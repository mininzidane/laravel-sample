<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SummaryPayments;
use Illuminate\Http\Request;

class SummaryPaymentsController extends Controller
{
	/**
	 * @var SummaryPayments
	 */
	private $reportModel;

	public function __construct(SummaryPayments $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
