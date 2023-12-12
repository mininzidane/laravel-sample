<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SummaryCategories;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummaryCategoriesController extends Controller
{
	/**
	 * @var SummaryCategories
	 */
	private $reportModel;

	public function __construct(SummaryCategories $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
