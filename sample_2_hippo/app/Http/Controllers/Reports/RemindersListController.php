<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Reports\RemindersList;
use App\Http\Controllers\Controller;

class RemindersListController extends Controller
{
	/**
	 * @var RemindersList
	 */
	private $reportModel;

	public function __construct(RemindersList $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
