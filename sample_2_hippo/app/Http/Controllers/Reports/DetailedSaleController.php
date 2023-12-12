<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DetailedSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetailedSaleController extends Controller
{
	/**
	 * @var DetailedSale
	 */
	private $reportModel;

	public function __construct(DetailedSale $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
