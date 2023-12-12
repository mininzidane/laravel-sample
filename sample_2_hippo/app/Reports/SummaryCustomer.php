<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryCustomer extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request))
			->setSalesStatusSql($request)
			->setSaleTypeSql($request)
			->setFormat($request)
			->setReplicaConnection($request);
		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$data = $locationsMap = [];
		$locationIds = explode(",", $this->getQueryParameters()["locations"]);
		sort($locationIds);
		/** @var object[] $results */
		$queryParameters = $this->getQueryParameters();
		unset($queryParameters["locations"]);
		foreach ($locationIds as $locationId) {
			$results = DB::connection($this->getConnectionName())->select(
				DB::raw(
					<<<SQL
											SELECT sale_items.customer_id AS customer_id,
												locations.id AS location_id,
												locations.name AS location_name,
												CONCAT(customers.first_name, ' ', customers.last_name) AS customer,
												COUNT(DISTINCT sale_items.invoice_id) AS number_sales,
												ROUND(SUM(sale_items.subtotal), 2) AS subtotal,
												ROUND(SUM(sale_items.total), 2) AS total,
												ROUND(SUM(sale_items.tax), 2) AS tax,
												ROUND(SUM(sale_items.profit), 2) AS profit
											FROM (SELECT raw_sale_items.invoice_id AS invoice_id,
													raw_sale_items.item_id AS item_id,
													raw_sale_items.line AS line,
													raw_sale_items.quantity AS quantity_purchased,
													raw_sale_items.cost_price AS item_cost_price,
													raw_sale_items.unit_price AS item_unit_price,
													SUM(IFNULL(invoice_item_taxes.percent, 0)) AS item_tax_percent,
													raw_sale_items.dispensing_fee AS dispensingfee,
													raw_sale_items.discount_percent AS discount_percent,
													raw_sale_items.total AS subtotal,
													raw_sale_items.serial_number AS serialnumber,
													raw_sale_items.description AS description,
													raw_sale_items.total * (1 + (SUM(IFNULL(invoice_item_taxes.percent, 0)) / 100)) AS total,
													raw_sale_items.total * (SUM(IFNULL(invoice_item_taxes.percent, 0)) / 100) AS tax,
													raw_sale_items.total - (raw_sale_items.cost_price * raw_sale_items.quantity) AS profit,
													raw_sales.owner_id AS customer_id
												FROM invoice_items AS raw_sale_items
												INNER JOIN invoices AS raw_sales
													ON raw_sale_items.invoice_id = raw_sales.id AND raw_sales.deleted_at IS NULL
												LEFT JOIN invoice_item_taxes AS invoice_item_taxes
													ON raw_sale_items.id = invoice_item_taxes.invoice_item_id AND invoice_item_taxes.deleted_at IS NULL
												WHERE raw_sales.status_id = :saleStatus
													AND raw_sale_items.deleted_at IS NULL
													AND CASE raw_sales.status_id
														WHEN 2 THEN DATE(CONVERT_TZ(raw_sales.completed_at, 'UTC', :timeZone1)) BETWEEN :beginDate1 AND :endDate1
														ELSE DATE(CONVERT_TZ(raw_sales.created_at, 'UTC', :timeZone2)) BETWEEN :beginDate2 AND :endDate2
														END
													AND raw_sale_items.quantity {$this->getSalesTypeSql()} 0
												GROUP BY
													raw_sale_items.invoice_id,
													raw_sale_items.id,
													raw_sale_items.item_id,
													raw_sale_items.line,
													raw_sale_items.quantity,
													raw_sale_items.cost_price,
													raw_sale_items.unit_price,
													raw_sale_items.dispensing_fee,
													raw_sale_items.discount_percent,
													raw_sale_items.total,
													raw_sale_items.serial_number,
													raw_sale_items.description,
													raw_sales.owner_id
											) sale_items
											INNER JOIN tblPatientOwnerInformation AS customers ON customers.id = sale_items.customer_id
											INNER JOIN invoices AS sales ON sale_items.invoice_id = sales.id
											LEFT JOIN tblOrganizationLocations AS locations ON locations.id = sales.location_id
											WHERE sales.location_id = :location
											GROUP BY sale_items.customer_id
											ORDER BY customer;
					SQL
					,
				),
				$queryParameters + ["location" => $locationId],
			);
			if (!empty($results)) {
				$data[$locationId] = $results;
				$locationsMap[$locationId] = $results[0]->location_name;
			}
		}

		return response()->json([
			"data" => $data,
			"locationsMap" => $locationsMap,
		]);
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
		];
	}
}
