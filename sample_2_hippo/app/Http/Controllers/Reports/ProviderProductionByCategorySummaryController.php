<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Reports\ProviderProductionByCategorySummary;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProviderProductionByCategorySummaryController extends Controller
{
	/**
	 * @var ProviderProductionByCategorySummary
	 */
	private $reportModel;

	public function __construct(
		ProviderProductionByCategorySummary $reportModel
	) {
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
