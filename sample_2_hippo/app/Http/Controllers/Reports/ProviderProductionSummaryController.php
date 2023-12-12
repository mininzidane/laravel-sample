<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Reports\ProviderProductionSummary;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProviderProductionSummaryController extends Controller
{
	/**
	 * @var ProviderProductionSummary
	 */
	private $reportModel;

	public function __construct(ProviderProductionSummary $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
