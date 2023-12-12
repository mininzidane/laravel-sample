<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Reports\AnniversaryList;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AnniversaryListController extends Controller
{
	/**
	 * @var AnniversaryList
	 */
	private $reportModel;

	public function __construct(AnniversaryList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
