<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\ClientStatements;
use Illuminate\Http\Request;

class ClientStatementsController extends Controller
{
	/**
	 * @var ClientStatements
	 */
	private $reportModel;

	public function __construct(ClientStatements $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request)
	{
		return $this->reportModel->generateReport($request);
	}
}
