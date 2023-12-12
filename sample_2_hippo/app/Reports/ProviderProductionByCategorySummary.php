<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Location;

class ProviderProductionByCategorySummary extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request));
		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$results = DB::connection($this->getConnectionName())->select(
			DB::raw("
			SELECT
					CONCAT_WS(', ', tblUsers.last_name, tblUsers.first_name) AS provider_name,
					IF(invoiceItemsSQ.category_name IS NULL, 'Uncategorized', invoiceItemsSQ.category_name) AS category_name,
					SUM(invoiceItemsSQ.total) AS total_amount,
					SUM(IF((invoicesSQ.status_id = 1), invoiceItemsSQ.total, 0)) AS open_total_amount,
					SUM(IF((invoicesSQ.status_id = 2), invoiceItemsSQ.total, 0)) AS collected_total_amount,
					ROUND(SUM(invoiceItemsSQ.total), 2) AS combined_total_amount
				FROM (
					SELECT
							type_id,
							total,
							provider_id,
							category_id,
							item_categories.name AS category_name,
							invoice_id
						FROM
							invoice_items
						LEFT OUTER JOIN item_categories ON invoice_items.category_id = item_categories.id
						WHERE
							invoice_items.deleted_at IS NULL) AS invoiceItemsSQ
						INNER JOIN (
							SELECT
									id, status_id
								FROM
									invoices
								WHERE
									invoices.location_id IN(:location)
									AND invoices.status_id IN(1, 2)
									AND CASE
										WHEN (invoices.status_id = 1) THEN (DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1)) BETWEEN :beginDate1 AND :endDate1)
										WHEN (invoices.status_id = 2) THEN (DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2)) BETWEEN :beginDate2 AND :endDate2)
							END) AS invoicesSQ ON invoiceItemsSQ.invoice_id = invoicesSQ.id
						LEFT JOIN tblUsers ON invoiceItemsSQ.provider_id = tblUsers.id
						WHERE invoiceItemsSQ.total != 0
			GROUP BY
				provider_id,
				category_name
			ORDER BY
				provider_id,
				category_name
                "),
			$this->getQueryParameters(),
		);

		$location = Location::on($this->getConnectionName())
			->where("id", $this->getQueryParameters()["location"])
			->with("subregion", "organization", "tz")
			->first();

		return response()->json([
			"results" => $results,
			"location" => $location,
			"beginDate1" => $this->getQueryParameters()["beginDate1"],
			"endDate1" => $this->getQueryParameters()["endDate1"],
		]);
	}

	public function buildParams(Request $request): array
	{
		return [
			"beginDate1" => $request->input("beginDate"),
			"beginDate2" => $request->input("beginDate"),
			"endDate1" => $request->input("endDate"),
			"endDate2" => $request->input("endDate"),
			"timeZone1" => $request->input("timeZone"),
			"timeZone2" => $request->input("timeZone"),
			"location" => $request->input("locations")[0],
		];
	}
}
