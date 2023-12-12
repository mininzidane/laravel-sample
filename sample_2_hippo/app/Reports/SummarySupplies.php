<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummarySupplies extends ReportModel
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
		// TODO: Remove OSPOS stuff.
		$results = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"
                SELECT suppliers.company_name AS supplier,
                       CONCAT(people.first_name, ' ', people.last_name) AS supplier_contact,
                       COUNT(DISTINCT sale_items.sale_id) AS number_sales,
                       SUM(sale_items.quantity_purchased) AS quantity_sold,
                       ROUND(SUM(sale_items.subtotal), 2) AS subtotal,
                       ROUND(SUM(sale_items.total), 2) AS total,
                       ROUND(SUM(sale_items.tax), 2) AS tax,
                       ROUND(SUM(sale_items.profit), 2) AS profit
                FROM (SELECT raw_sale_items.sale_id AS sale_id,
                             raw_sale_items.item_id AS item_id,
                             raw_sale_items.line AS line,
                             raw_sale_items.quantity_purchased AS quantity_purchased,
                             raw_sale_items.item_cost_price AS item_cost_price,
                             raw_sale_items.item_unit_price AS item_unit_price,
                             SUM(IFNULL(sale_item_taxes.percent, 0)) AS item_tax_percent,
                             raw_sale_items.dispensing_fee AS dispensingfee,
                             raw_sale_items.discount_percent AS discount_percent,
                             raw_sale_items.item_line_total AS subtotal,
                             raw_sale_items.serialnumber AS serialnumber,
                             raw_sale_items.description AS description,
                             raw_sale_items.item_line_total * (1 + (SUM(IFNULL(sale_item_taxes.percent, 0)) / 100)) AS total,
                             raw_sale_items.item_line_total * (SUM(IFNULL(sale_item_taxes.percent, 0)) / 100) AS tax,
                             raw_sale_items.item_line_total - (raw_sale_items.item_cost_price * raw_sale_items.quantity_purchased) AS profit
                     FROM ospos_sales_items AS raw_sale_items
                     INNER JOIN ospos_sales AS raw_sales
                       ON raw_sale_items.sale_id = raw_sales.sale_id
                     LEFT JOIN ospos_sales_items_taxes AS sale_item_taxes
                       ON raw_sale_items.sale_id = sale_item_taxes.sale_id
                       AND raw_sale_items.item_id = sale_item_taxes.item_id
                       AND raw_sale_items.line = sale_item_taxes.line
                     WHERE raw_sales.sale_status = :saleStatus
                        AND raw_sales." .
					$this->getSalesStatusSql() .
					"
                        BETWEEN :beginDate AND :endDate
                        AND raw_sale_items.quantity_purchased " .
					$this->getSalesTypeSql() .
					" 0
                     GROUP BY raw_sale_items.sale_id,
                              raw_sale_items.item_id,
                              raw_sale_items.line) sale_items
                INNER JOIN ospos_items AS items
                  ON sale_items.item_id = items.item_id
                INNER JOIN ospos_suppliers AS suppliers
                  ON suppliers.person_id = items.supplier_id
                INNER JOIN ospos_people_old_table AS people
                  ON suppliers.person_id = people.person_id
                INNER JOIN ospos_sales AS sales
	              ON sale_items.sale_id = sales.sale_id
                WHERE FIND_IN_SET(sales.location_id, :locations)
                GROUP BY items.supplier_id
                ORDER BY suppliers.company_name;
            ",
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
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
			"saleStatus" => $request->input("saleStatus"),
		];
	}
}
