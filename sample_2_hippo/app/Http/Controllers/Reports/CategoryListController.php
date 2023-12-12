<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\CategoryList;
use Illuminate\Http\Request;

class CategoryListController extends Controller
{
	/**
	 * @var CategoryList
	 */
	private $reportModel;

	public function __construct(CategoryList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
