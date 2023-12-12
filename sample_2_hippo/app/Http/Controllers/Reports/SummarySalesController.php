<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SummarySales;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummarySalesController extends Controller
{
	/**
	 * @var SummarySales
	 */
	private $reportModel;

	public function __construct(SummarySales $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
