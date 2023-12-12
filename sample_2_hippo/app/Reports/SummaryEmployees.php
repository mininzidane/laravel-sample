<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryEmployees extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request))
			->setSalesStatusSql($request)
			->setSaleTypeSql($request)
			->setReplicaConnection($request);

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$results = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"SELECT CONCAT(employees.first_name, ' ', employees.last_name) AS employee,
					   COUNT(DISTINCT invoice_items.invoice_id) AS number_sales,
					   ROUND(SUM(invoice_items.subtotal), 2) AS subtotal,
					   ROUND(SUM(invoice_items.total), 2) AS total,
					   ROUND(SUM(invoice_items.tax), 2) AS tax,
					   ROUND(SUM(invoice_items.profit), 2) AS profit
				FROM (
					SELECT invoice_items.invoice_id AS invoice_id,
							 invoice_items.item_id AS item_id,
							 invoice_items.line AS line,
							 invoice_items.quantity AS quantity,
							 invoice_items.cost_price AS cost_price,
							 invoice_items.unit_price AS unit_price,
							 SUM(IFNULL(invoice_item_taxes.percent, 0)) AS item_tax_percent,
							 invoice_items.dispensing_fee AS dispensing_fee,
							 invoice_items.discount_percent AS discount_percent,
							 invoice_items.total AS subtotal,
							 invoice_items.serial_number AS serial_number,
							 invoice_items.description AS description,
							 ROUND(invoice_items.total * (1 + (SUM(IFNULL(invoice_item_taxes.percent, 0)) / 100)),2) AS total,
							 ROUND(invoice_items.total * (SUM(IFNULL(invoice_item_taxes.percent, 0)) / 100),2) AS tax,
							 ROUND(invoice_items.total - (invoice_items.cost_price * invoice_items.quantity),2) AS profit,
							 invoice_items.provider_id
					FROM invoice_items
					INNER JOIN invoices AS invoices
						ON invoice_items.invoice_id = invoices.id
						AND invoices.deleted_at IS NULL
					LEFT JOIN invoice_item_taxes
						 ON invoice_items.id = invoice_item_taxes.invoice_item_id
						AND invoice_item_taxes.deleted_at IS NULL
					WHERE invoices.status_id = :saleStatus
						AND invoice_items.deleted_at IS NULL
						AND CASE invoices.status_id
							WHEN 2 THEN DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone1)) BETWEEN :beginDate1 AND :endDate1
							ELSE DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone2)) BETWEEN :beginDate2 AND :endDate2
						END
						AND CASE :saleType
							WHEN 1 THEN invoice_items.quantity > 0
							ELSE invoice_items.quantity < 0
						END
					GROUP BY invoice_items.invoice_id,
							 invoice_items.item_id,
							 invoice_items.line) invoice_items
				INNER JOIN tblUsers AS employees
					ON invoice_items.provider_id = employees.id
				INNER JOIN invoices
					ON invoice_items.invoice_id = invoices.id
				WHERE FIND_IN_SET(invoices.location_id, :locations)
				GROUP BY invoice_items.provider_id
				ORDER BY employees.last_name",
			),
			$this->getQueryParameters(),
		);

		return response()->json($results);
	}

	/**
	 * @param Request $request
	 * @return array[]
	 * Set your items to change columns and conditionals in the flags section
	 */
	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
			"beginDate1" => $request->input("beginDate"),
			"beginDate2" => $request->input("beginDate"),
			"endDate1" => $request->input("endDate"),
			"endDate2" => $request->input("endDate"),
			"timeZone1" => $request->input("timeZone"),
			"timeZone2" => $request->input("timeZone"),
			"saleStatus" => $request->input("saleStatus"),
			"saleType" => $request->input("saleType"),
		];
	}
}
