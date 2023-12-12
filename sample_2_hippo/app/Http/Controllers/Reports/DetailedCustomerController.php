<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\DetailedCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetailedCustomerController extends Controller
{
	/**
	 * @var DetailedCustomer
	 */
	private $reportModel;

	public function __construct(DetailedCustomer $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
