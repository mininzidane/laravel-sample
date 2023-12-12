<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\InventoryItemList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryItemListController extends Controller
{
	/**
	 * @var InventoryItemList
	 */
	private $reportModel;

	public function __construct(InventoryItemList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
