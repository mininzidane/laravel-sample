<?php

namespace App\Http\Controllers;

use App\Models\LabRequisition;
use Exception;
use Illuminate\Http\Request;
use Log;

class LabRequisitionController extends Controller
{
	public function __construct()
	{
	}

	public function index($subdomainName, $id, $integration = "ANTECH")
	{
		$this->createSubdomainConnection($subdomainName);

		$requisitions = null;

		$requisitions = LabRequisition::on($subdomainName)
			->waiting()
			->with("antechOrderCode")
			->where("location_id", "=", $id)
			->where("integration", "=", strtoupper($integration))
			->get();

		return response($requisitions);
	}

	public function waiting()
	{
		$requisitionsRequiringProcessing = LabRequisition::waiting()->get();

		return response($requisitionsRequiringProcessing);
	}

	public function update(Request $request, $subdomainName, $requisitionId)
	{
		$this->createSubdomainConnection($subdomainName);

		try {
			$requisition = LabRequisition::on($subdomainName)->findOrFail(
				$requisitionId,
			);
		} catch (Exception $e) {
			Log::warning(
				"Requisition ID not found in tblRequisitions. Subdomain: " .
					$subdomainName .
					" Id:" .
					$requisitionId,
			);
			return null;
		}

		$requisition->status = $request->get("status");

		$requisition->save();

		return $requisition;
	}

	public function destroy($id)
	{
		return response(LabRequisition::destroy($id));
	}
}
