<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SummaryEmployees;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummaryEmployeesController extends Controller
{
	/**
	 * @var SummaryEmployees
	 */
	private $reportModel;

	public function __construct(SummaryEmployees $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
