<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryItems extends ReportModel
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
				"SELECT items.id AS item_id,
					   items.number AS item_number,
					   items.name AS item_name,
					   COUNT(DISTINCT invoice_items.invoice_id) AS number_sales,
					   COUNT(DISTINCT invoice_items.customer_id) AS number_customers,
					   ROUND(SUM(invoice_items.quantity), 2) AS quantity,
					   ROUND(SUM(invoice_items.subtotal), 2) AS subtotal,
					   ROUND(SUM(invoice_items.total), 2) AS total,
					   ROUND(SUM(invoice_items.tax), 2) AS tax,
					   ROUND(SUM(invoice_items.profit), 2) AS profit
				FROM (SELECT raw_invoice_items.invoice_id AS invoice_id,
							 raw_invoice_items.item_id AS item_id,
							 raw_invoice_items.line AS line,
							 raw_invoice_items.quantity AS quantity,
							 raw_invoice_items.price AS price,
							 raw_invoice_items.cost_price AS cost_price,
							 raw_invoice_items.unit_price AS unit_price,
							 SUM(IFNULL(invoice_item_taxes.percent, 0)) AS item_tax_percent,
							 raw_invoice_items.dispensing_fee AS dispensing_fee,
							 raw_invoice_items.discount_percent AS discount_percent,
							 raw_invoice_items.total AS subtotal,
							 raw_invoice_items.serial_number AS serial_number,
							 raw_invoice_items.description AS description,
							 raw_invoice_items.total * (1 + (SUM(IFNULL(invoice_item_taxes.percent, 0)) / 100)) AS total,
							 raw_invoice_items.total * (SUM(IFNULL(invoice_item_taxes.percent, 0)) / 100) AS tax,
							 raw_invoice_items.total - (raw_invoice_items.cost_price * raw_invoice_items.quantity) AS profit,
							 raw_invoices.owner_id AS customer_id
					 FROM invoice_items AS raw_invoice_items
					 INNER JOIN invoices AS raw_invoices
					   ON raw_invoice_items.invoice_id = raw_invoices.id
						 AND raw_invoices.deleted_at IS NULL
					 LEFT JOIN invoice_item_taxes AS invoice_item_taxes
					   ON raw_invoice_items.id = invoice_item_taxes.invoice_item_id
						 AND invoice_item_taxes.deleted_at IS NULL
					 WHERE raw_invoices.status_id = :saleStatus
					 	AND raw_invoice_items.deleted_at IS NULL
						AND DATE(CONVERT_TZ(raw_invoices." .
					$this->getSalesStatusSql() .
					", 'UTC', :timeZone)) BETWEEN :beginDate AND :endDate
						AND raw_invoice_items.quantity " .
					$this->getSalesTypeSql() .
					" 0
					 GROUP BY raw_invoice_items.invoice_id,
							  raw_invoice_items.item_id,
							  raw_invoice_items.line) invoice_items
				INNER JOIN items AS items
				  ON invoice_items.item_id = items.id
				INNER JOIN invoices AS invoices
				  ON invoice_items.invoice_id = invoices.id
				WHERE FIND_IN_SET(invoices.location_id, :locations)
				GROUP BY invoice_items.item_id
				ORDER BY items.name",
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
			"timeZone" => $request->input("timeZone"),
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
			"saleStatus" => $request->input("saleStatus"),
		];
	}
}
