<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryCategories extends ReportModel
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
				"SELECT IFNULL(categories.name, 'Uncategorized') AS category,
					   COUNT(DISTINCT invoice_items.invoice_id) AS number_sales,
					   SUM(invoice_items.quantity) AS quantity_sold,
					   ROUND(SUM(invoice_items.subtotal), 2) AS subtotal,
					   ROUND(SUM(invoice_items.total), 2) AS total,
					   ROUND(SUM(invoice_items.tax), 2) AS tax,
					   ROUND(SUM(invoice_items.profit), 2) AS profit
				FROM (SELECT raw_invoice_items.invoice_id AS invoice_id,
							 raw_invoice_items.item_id AS item_id,
							 raw_invoice_items.category_id AS category_id,
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
							 raw_invoice_items.total - (raw_invoice_items.cost_price * raw_invoice_items.quantity) AS profit
					  FROM invoice_items AS raw_invoice_items
					  INNER JOIN invoices AS raw_invoices
						  ON raw_invoice_items.invoice_id = raw_invoices.id
							AND raw_invoices.deleted_at IS NULL
					  LEFT JOIN invoice_item_taxes AS raw_invoice_item_taxes
						  ON raw_invoice_items.id = raw_invoice_item_taxes.invoice_item_id
							AND raw_invoice_item_taxes.deleted_at IS NULL
					  WHERE 
					    raw_invoice_items.deleted_at IS NULL
					    AND FIND_IN_SET(raw_invoices.location_id, :locations)
					    AND raw_invoices.status_id = :saleStatus
					    AND CASE raw_invoices.status_id
						  WHEN 2 THEN DATE(CONVERT_TZ(raw_invoices.completed_at, 'UTC', :timeZone1)) BETWEEN :beginDate1 AND :endDate1
						  ELSE DATE(CONVERT_TZ(raw_invoices.created_at, 'UTC', :timeZone2)) BETWEEN :beginDate2 AND :endDate2
					    END
					  GROUP BY raw_invoice_items.id) invoice_items
				LEFT JOIN item_categories AS categories
					ON invoice_items.category_id = categories.id
				GROUP BY invoice_items.category_id
				ORDER BY categories.name",
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
			"endDate1" => $request->input("endDate"),
			"beginDate2" => $request->input("beginDate"),
			"endDate2" => $request->input("endDate"),
			"timeZone1" => $request->input("timeZone"),
			"timeZone2" => $request->input("timeZone"),
			"saleStatus" => $request->input("saleStatus"),
		];
	}
}
