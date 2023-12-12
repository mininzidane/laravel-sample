<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\SurgeryCount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurgeryCountController extends Controller
{
	private $reportModel;

	public function __construct(SurgeryCount $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
