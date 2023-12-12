<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Reports\DetailedProviderProduction;

class DetailedProviderProductionController extends Controller
{
	/**
	 * @var DetailedProviderProduction
	 */
	private $reportModel;

	public function __construct(DetailedProviderProduction $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
