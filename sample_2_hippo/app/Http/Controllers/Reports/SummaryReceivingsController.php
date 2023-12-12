<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DetailedCustomer;
use App\Reports\SummaryReceivings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummaryReceivingsController extends Controller
{
	/**
	 * @var DetailedCustomer
	 */
	private $reportModel;

	public function __construct(SummaryReceivings $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
