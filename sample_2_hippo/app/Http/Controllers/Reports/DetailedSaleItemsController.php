<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DetailedSaleItems;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetailedSaleItemsController extends Controller
{
	/**
	 * @var DetailedSaleItems
	 */
	private $reportModel;

	public function __construct(DetailedSaleItems $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
