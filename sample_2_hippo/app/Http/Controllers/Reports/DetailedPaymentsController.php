<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Reports\DetailedPayments;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class DetailedPaymentsController extends Controller
{
	/**
	 * @var DetailedPayments
	 */
	private $reportModel;

	public function __construct(DetailedPayments $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
