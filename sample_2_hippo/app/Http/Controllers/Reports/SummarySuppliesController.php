<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SummarySupplies;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummarySuppliesController extends Controller
{
	/**
	 * @var SummarySupplies
	 */
	private $reportModel;

	public function __construct(SummarySupplies $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
