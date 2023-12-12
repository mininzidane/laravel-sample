<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ItemKitList;
use Illuminate\Http\Request;

class ItemKitListController extends Controller
{
	/**
	 * @var ItemKitList
	 */
	private $reportModel;

	public function __construct(ItemKitList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
