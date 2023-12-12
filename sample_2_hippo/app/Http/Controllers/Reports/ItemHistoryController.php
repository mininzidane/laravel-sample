<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ItemHistory;
use Illuminate\Http\Request;

class ItemHistoryController extends Controller
{
	/**
	 * @var ItemHistory
	 */
	private $reportModel;

	public function __construct(ItemHistory $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
