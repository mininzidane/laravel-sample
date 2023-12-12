<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\AgingSummary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummaryAgingController extends Controller
{
	private $reportModel;

	public function __construct(AgingSummary $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
