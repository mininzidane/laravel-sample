<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\InventorySummary;
use Illuminate\Http\Request;

class InventorySummaryController extends Controller
{
	/**
	 * @var InventorySummary
	 */
	private $reportModel;

	public function __construct(InventorySummary $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
