<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SummaryCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummaryCustomerController extends Controller
{
	/**
	 * @var SummaryCustomer
	 */
	private $reportModel;

	public function __construct(SummaryCustomer $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
