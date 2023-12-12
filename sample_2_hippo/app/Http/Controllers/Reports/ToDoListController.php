<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Reports\ToDoList;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ToDoListController extends Controller
{
	/**
	 * @var ToDoList
	 */
	private $reportModel;

	public function __construct(ToDoList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
