<?php

namespace App\Reports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositSlip extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters([
			"locations" => $request->input("locations"),
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
		]);
		parent::__construct();
	}

	public function generateReportData()
	{
		$locations = $this->getQueryParameters()["locations"];
		$beginDate = $this->getQueryParameters()["beginDate"];
		$endDate = $this->getQueryParameters()["endDate"];

		if (count($locations) === 1 && $beginDate && $endDate) {
			$locationData = DB::connection($this->getConnectionName())
				->table("tblOrganizationLocations")
				->leftJoin(
					"tblSubRegions",
					"tblSubRegions.id",
					"=",
					"tblOrganizationLocations.state",
				)
				->leftJoin(
					"tblTimezones",
					"tblTimezones.id",
					"=",
					"tblOrganizationLocations.timezone",
				)
				->select([
					"tblOrganizationLocations.id",
					"tblOrganizationLocations.name",
					"tblOrganizationLocations.address1",
					"tblOrganizationLocations.address2",
					"tblOrganizationLocations.address3",
					"tblOrganizationLocations.city",
					"tblOrganizationLocations.zip",
					"tblOrganizationLocations.phone1",
					"tblOrganizationLocations.phone2",
					"tblOrganizationLocations.phone3",
					"tblOrganizationLocations.fax",
					"tblOrganizationLocations.fax",
					"tblSubRegions.name as state_name",
					"tblTimezones.php_supported",
				])
				->where("tblOrganizationLocations.id", $locations[0])
				->first();

			$paymentData = DB::connection($this->getConnectionName())
				->table("payments")
				->join(
					"payment_methods",
					"payment_methods.id",
					"=",
					"payments.payment_method_id",
				)
				->join(
					"invoice_payments",
					"invoice_payments.payment_id",
					"=",
					"payments.id",
				)
				->join(
					"invoices",
					"invoices.id",
					"=",
					"invoice_payments.invoice_id",
				)
				->select([
					"invoice_payments.amount_applied as payment_amount",
					"payment_methods.name as payment_method",
				])
				->where("payment_methods.is_depositable", "=", true)
				->where("invoices.location_id", "=", $locations[0])
				->whereBetween("payments.received_at", [$beginDate, $endDate])
				->get();

			if ($locationData && $paymentData) {
				return response()->json([
					"location" => $locationData,
					"payments" => $paymentData,
					"beginDate" => $beginDate,
					"endDate" => $endDate,
				]);
			}

			return response()->json(
				"No data was found for that date range.",
				404,
			);
		}

		return response()->json(
			"Invalid parameters were passed. locations, beginDate, & endDate are required.",
			400,
		);
	}

	private function getUTCDateFromDateString($dateString, $locale): string
	{
		return Carbon::createFromFormat("Y-m-d", $dateString, $locale)
			->setTimezone("UTC")
			->toDateString();
	}
}
