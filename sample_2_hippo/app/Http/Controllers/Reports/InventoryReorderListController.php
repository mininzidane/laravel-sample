<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Reports\InventoryReorderList;

class InventoryReorderListController extends Controller
{
	/**
	 * @var InventoryReorderList
	 */
	private $reportModel;

	public function __construct(InventoryReorderList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
