<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\VaccineCertificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaccineCertificateController extends Controller
{
	/**
	 * @var VaccineCertificate
	 */
	private $reportModel;

	public function __construct(VaccineCertificate $reportModel)
	{
		$this->reportModel = $reportModel;
	}

	public function index(Request $request): JsonResponse
	{
		return $this->reportModel->generateReport($request);
	}
}
