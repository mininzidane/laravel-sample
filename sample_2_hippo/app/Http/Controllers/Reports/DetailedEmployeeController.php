<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DetailedEmployee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetailedEmployeeController extends Controller
{
	/**
	 * @var DetailedEmployee
	 */
	private $reportModel;

	public function __construct(DetailedEmployee $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
