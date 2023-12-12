<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\InventoryExpirationList;
use Illuminate\Http\Request;

class InventoryExpirationListController extends Controller
{
	/**
	 * @var InventoryItemList
	 */
	private $reportModel;

	public function __construct(InventoryExpirationList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
