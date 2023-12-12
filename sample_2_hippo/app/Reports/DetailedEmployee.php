<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailedEmployee extends ReportModel
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
                        CONCAT(tblUsers.first_name, ' ', tblUsers.last_name) as employee,
                        invoice_items.invoice_id AS invoice_id,
                        date(invoice_items.created_at) AS created_at,
                        date(invoice_items.completed_at) AS completed_at,
                        CONCAT(customers.first_name, ' ', customers.last_name) AS customer_name,
                        ROUND(SUM(invoice_items.subtotal) ,2) AS subtotal,
                        ROUND(SUM(invoice_items.total), 2) AS total,
                        ROUND(SUM(invoice_items.tax), 2) AS tax,
                        ROUND(SUM(invoice_items.profit), 2) AS profit
				FROM (SELECT raw_invoice_items.invoice_id AS invoice_id,
							raw_invoice_items.item_id AS item_id,
							raw_invoice_items.line AS line,
							raw_invoice_items.cost_price AS item_cost_price,
							raw_invoice_items.unit_price AS item_unit_price,
							SUM(IFNULL(invoice_item_taxes.percent, 0)) AS item_tax_percent,
							raw_invoice_items.dispensing_fee AS dispensing_fee,
							raw_invoice_items.discount_percent AS discount_percentage,
							raw_invoice_items.total AS subtotal,
							raw_invoice_items.description AS description,
							ROUND(raw_invoice_items.total * (1+(SUM(IFNULL(invoice_item_taxes.percent, 0))/100)),2) AS total,
							ROUND(raw_invoice_items.total * (SUM(IFNULL(invoice_item_taxes.percent, 0))/100),2) AS tax,
							ROUND(raw_invoice_items.total - (raw_invoice_items.cost_price * raw_invoice_items.quantity),2) AS profit,
							CONVERT_TZ(raw_invoices.created_at, 'UTC', :timeZone1) AS created_at,
							CONVERT_TZ(raw_invoices.completed_at, 'UTC', :timeZone2) AS completed_at,
							raw_invoice_items.provider_id AS user_id,
							raw_invoices.location_id AS location_id,
							raw_invoices.owner_id AS owner_id
					FROM invoice_items AS raw_invoice_items 
					INNER JOIN invoices AS raw_invoices
					  ON raw_invoice_items.invoice_id = raw_invoices.id
					 AND raw_invoices.deleted_at IS NULL
					LEFT JOIN invoice_item_taxes AS invoice_item_taxes
					  ON raw_invoice_items.id = invoice_item_taxes.invoice_item_id
					  AND invoice_item_taxes.deleted_at is null
					WHERE raw_invoices.status_id = :saleStatus
                        AND CASE raw_invoices.status_id
                            WHEN 2 THEN DATE(CONVERT_TZ(raw_invoices.completed_at, 'UTC', :timeZone3)) BETWEEN :beginDate1 AND :endDate1
                            ELSE DATE(CONVERT_TZ(raw_invoices.created_at, 'UTC', :timeZone4)) BETWEEN :beginDate2 AND :endDate2
                        END
 					  AND raw_invoice_items.quantity " .
					$this->getSalesTypeSql() .
					" 0
					  AND raw_invoice_items.deleted_at is null
					GROUP BY raw_invoice_items.invoice_id,
							  raw_invoice_items.item_id,
							  raw_invoice_items.line) invoice_items
				INNER JOIN tblPatientOwnerInformation AS customers
				  ON invoice_items.owner_id = customers.id
				INNER JOIN tblUsers on tblUsers.id = invoice_items.user_id  
				INNER JOIN tblOrganizationLocations AS locations
				  ON invoice_items.location_id = locations.id
				WHERE FIND_IN_SET(invoice_items.location_id, :locations)
				AND invoice_items.user_id = :employeeId
				GROUP BY employee, 
				         invoice_items.invoice_id, 
				         invoice_items.created_at,  
				         invoice_items.completed_at
				ORDER BY invoice_items.completed_at,
						invoice_items.created_at",
			),
			$this->getQueryParameters(),
		);

		return response()->json($results);
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
			"timeZone1" => $request->input("timeZone"),
			"timeZone2" => $request->input("timeZone"),
			"timeZone3" => $request->input("timeZone"),
			"timeZone4" => $request->input("timeZone"),
			"saleStatus" => $request->input("saleStatus"),
			"beginDate1" => $request->input("beginDate"),
			"endDate1" => $request->input("endDate"),
			"beginDate2" => $request->input("beginDate"),
			"endDate2" => $request->input("endDate"),
			"employeeId" => $request->input("detailId"),
		];
	}
}
