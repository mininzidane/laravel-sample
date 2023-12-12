<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ControlledSubstanceLog;
use Illuminate\Http\Request;

class ControlledSubstanceLogController extends Controller
{
	/**
	 * @var ControlledSubstanceLog
	 */
	private $reportModel;

	public function __construct(ControlledSubstanceLog $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
