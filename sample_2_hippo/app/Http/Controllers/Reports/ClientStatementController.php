<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ClientStatement;
use Illuminate\Http\Request;

class ClientStatementController extends Controller
{
	/**
	 * @var ClientStatement
	 */
	private $reportModel;

	public function __construct(ClientStatement $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
