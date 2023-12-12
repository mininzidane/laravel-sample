<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Reports\CondolencesList;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CondolencesListController extends Controller
{
	/**
	 * @var CondolencesList
	 */
	private $reportModel;

	public function __construct(CondolencesList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
