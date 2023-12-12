<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummarySales extends ReportModel
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
				"SELECT
					invoice_items.invoice_date AS invoice_date,
					COUNT(DISTINCT invoice_items.invoice_id) AS number_sales,
					ROUND(SUM(invoice_items.subtotal), 2) AS subtotal,
					ROUND(SUM(invoice_items.total), 2) AS total,
					ROUND(SUM(invoice_items.tax), 2) AS tax,
					ROUND(SUM(invoice_items.profit), 2) AS profit
				FROM (
					SELECT 
						raw_invoice_items.invoice_id AS invoice_id,
						raw_invoice_items.item_id AS item_id,
						raw_invoice_items.line AS line,
						raw_invoice_items.quantity AS quantity,
						raw_invoice_items.price AS price,
						raw_invoice_items.cost_price AS cost_price,
						raw_invoice_items.unit_price AS unit_price,
						SUM(IFNULL(raw_invoice_item_taxes.percent, 0)) AS item_tax_percent,
						raw_invoice_items.dispensing_fee AS dispensing_fee,
						raw_invoice_items.discount_percent AS discount_percent,
						raw_invoice_items.total AS subtotal,
						raw_invoice_items.serial_number AS serial_number,
						raw_invoice_items.description AS description,
						raw_invoice_items.total + SUM(IFNULL(raw_invoice_item_taxes.amount, 0)) AS total,
						SUM(IFNULL(raw_invoice_item_taxes.amount, 0)) AS tax,
						raw_invoice_items.total - (raw_invoice_items.cost_price * raw_invoice_items.quantity) AS profit,
						raw_invoices.created_at AS created_at,
						raw_invoices.completed_at AS completed_at,
						CASE raw_invoices.status_id
							WHEN 2 THEN DATE(CONVERT_TZ(raw_invoices.completed_at, 'UTC', :timeZone1))
							ELSE DATE(CONVERT_TZ(raw_invoices.created_at, 'UTC', :timeZone2))
						END AS invoice_date
					FROM invoice_items AS raw_invoice_items
					INNER JOIN invoices AS raw_invoices
						ON raw_invoice_items.invoice_id = raw_invoices.id
						AND raw_invoices.deleted_at IS NULL
					LEFT JOIN invoice_item_taxes AS raw_invoice_item_taxes
						ON raw_invoice_items.id = raw_invoice_item_taxes.invoice_item_id
						AND raw_invoice_item_taxes.deleted_at IS NULL
					WHERE raw_invoice_items.deleted_at IS NULL
						AND FIND_IN_SET(raw_invoices.location_id, :locations)
						AND raw_invoices.status_id = :saleStatus
						AND CASE raw_invoices.status_id
							WHEN 2 THEN DATE(CONVERT_TZ(raw_invoices.completed_at, 'UTC', :timeZone3)) BETWEEN :beginDate1 AND :endDate1
							ELSE DATE(CONVERT_TZ(raw_invoices.created_at, 'UTC', :timeZone4)) BETWEEN :beginDate2 AND :endDate2
						END
					GROUP BY raw_invoice_items.id) AS invoice_items
				GROUP BY invoice_date
				ORDER BY invoice_date",
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
			"saleStatus" => $request->input("saleStatus"),
			"timeZone1" => $request->input("timeZone"),
			"timeZone2" => $request->input("timeZone"),
			"timeZone3" => $request->input("timeZone"),
			"timeZone4" => $request->input("timeZone"),
		];
	}
}
