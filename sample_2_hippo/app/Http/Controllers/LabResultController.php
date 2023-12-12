<?php

namespace App\Http\Controllers;

use App\Models\LabRequisition;
use App\Models\LabTest;
use App\Models\LabTestFolder;
use App\Models\Organization;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LabResultController extends Controller
{
	public function save(Request $request, $subdomain, $type = "ANTECH")
	{
		$this->createSubdomainConnection($subdomain);

		$s3Filename = $request->get("filename");
		$requisitionId = intval($request->get("requisitionId"));

		$requisition = null;

		$type = strtoupper($type);

		if ($type == "ANTECH") {
			try {
				$requisition = LabRequisition::on($subdomain)
					->with("user", "antechOrderCode")
					->find($requisitionId);
			} catch (Exception $e) {
				error_log($e);
			}
		}

		if ($type == "ZOETIS") {
			try {
				$requisition = LabRequisition::on($subdomain)
					->with("user", "zoetisOrderCode")
					->find($requisitionId);
			} catch (Exception $e) {
				error_log($e);
			}
		}

		if (!$requisition) {
			return response("Invalid Requisition ID");
		}

		$organization = Organization::on($subdomain)->firstOrFail();

		try {
			$date = (new Carbon($requisition->created_at))->format("Y-m-d");
		} catch (Exception $e) {
		}

		if ($type == "ANTECH") {
			$orderDescription = $requisition->custom_order_code;

			if ($requisition->custom_order_code == null) {
				$orderDescription = $requisition->antechOrderCode->description;
			}
		}

		if ($type == "ZOETIS") {
			$orderDescription = $requisition->zoetisOrderCode->description;
		}

		$patient_name =
			$requisition->patient->first_name ?? "NO_PATIENT_FIRST_NAME";

		$fancyFolderName =
			$patient_name .
			" - " .
			$orderDescription .
			" - " .
			$date .
			" (" .
			$requisition->id .
			")";
		$fancyFilename = $fancyFolderName . ".pdf";

		$labTestFolder = LabTestFolder::on($subdomain)->firstOrCreate([
			"title" => ucfirst($type) . " - " . $fancyFolderName,
			"client_id" => $requisition->client_id,
			"organization_id" => $organization->id,
			"added_by" => $requisition->user_id,
		]);

		$labTestFolder->removed = 0;
		$labTestFolder->save();

		$labTest = LabTest::on($subdomain)->firstOrCreate([
			"organization_id" => $organization->id,
			"lab_id" => $labTestFolder->id,
			"display_name" => $fancyFilename,
		]);

		$labTest->name = $s3Filename;
		$labTest->save();

		return $labTest;
	}

	public function codes()
	{
		$this->createSubdomainConnection("subone");

		$requistion = LabRequisition::on("subone")
			->with("antechOrderCode")
			->find(5);

		return response($requistion);
	}
}
