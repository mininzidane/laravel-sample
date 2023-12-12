<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DetailedReceiving;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetailedReceivingController extends Controller
{
	/**
	 * @var DetailedReceiving
	 */
	private $reportModel;

	public function __construct(DetailedReceiving $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
