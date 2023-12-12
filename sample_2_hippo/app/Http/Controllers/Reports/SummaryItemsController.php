<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SummaryItems;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SummaryItemsController extends Controller
{
	/**
	 * @var SummaryItems
	 */
	private $reportModel;

	public function __construct(SummaryItems $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
