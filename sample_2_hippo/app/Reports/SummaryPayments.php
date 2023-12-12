<?php

namespace App\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryPayments extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters(
			$this->buildParams($request),
		)->setReplicaConnection($request);

		parent::__construct();
	}

	public function generateReportData()
	{
		$locations = $this->getQueryParameters()["locations"];
		$beginDate = $this->getQueryParameters()["beginDate"];
		$endDate = $this->getQueryParameters()["endDate"];

		$results = DB::connection($this->getConnectionName())
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
				"payment_methods.name as payment_method",
				DB::raw(
					"COUNT(DISTINCT `invoice_payments`.`id`) as `number_payments`",
				), //distinct
				DB::raw("SUM(`invoice_payments`.`amount_applied`) as `total`"),
			])
			->whereIn("invoices.location_id", $locations)
			->whereBetween("payments.received_at", [$beginDate, $endDate])
			->groupBy("payment_methods.id")
			->orderBy("total", "desc")
			->get();

		return response()->json($results);
	}

	/**
	 * @param Request $request
	 * @return array[]
	 * Set your items to change columns and conditionals in the flags section
	 */
	public function buildParams(Request $request): array
	{
		// Reporting App - ReportClass setLocations() uses implode
		// using explode to revert comma-delimited-string back to an array of locations
		return [
			"locations" => $request->input("locations"),
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
		];
	}
}
