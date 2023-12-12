<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\VaccineHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaccineHistoryController extends Controller
{
	/**
	 * @var VaccineHistory
	 */
	private $reportModel;

	public function __construct(VaccineHistory $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
