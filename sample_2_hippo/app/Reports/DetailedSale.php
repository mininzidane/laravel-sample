<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailedSale extends ReportModel
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
					invoice_items.invoice_id AS invoice_id,
					CONVERT_TZ(invoice_items.invoice_created_at, 'UTC', :timeZone1) AS invoice_created_at,
					CONVERT_TZ(invoice_items.invoice_completed_at, 'UTC', :timeZone2) AS invoice_completed_at,
					CONVERT_TZ(invoice_items.invoice_date, 'UTC', :timeZone3) AS invoice_date,
					SUM(invoice_items.quantity) AS quantity,
					CONCAT(users.first_name, ' ', users.last_name) AS employee_name,
					owners.id as customer_id,
					CONCAT(owners.first_name, ' ', owners.last_name) AS customer_name,
					clients.id as client_id,
					clients.first_name as client_name,
					SUM(invoice_items.subtotal) AS subtotal,
					SUM(invoice_items.total) AS total,
					SUM(invoice_items.tax) AS tax,
					SUM(invoice_items.profit) AS profit,
					invoice_items.invoice_comment AS comment,
					invoice_items.location_id AS location_id,
					locations.name AS location_name
				FROM (
					SELECT 
						raw_invoice_items.invoice_id AS invoice_id,
						raw_invoices.patient_id AS invoice_patient_id,
						raw_invoices.owner_id AS invoice_owner_id,
						raw_invoices.location_id AS location_id,
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
						raw_invoices.created_at AS invoice_created_at,
						raw_invoices.completed_at AS invoice_completed_at,
						raw_invoice_items.provider_id AS provider_id,
						raw_invoices.user_id AS invoice_user_id,
						raw_invoices.comment AS invoice_comment,
						CASE raw_invoices.status_id
							WHEN 2 THEN raw_invoices.completed_at
							ELSE raw_invoices.created_at
						END AS invoice_date
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
							WHEN 2 THEN DATE(CONVERT_TZ(raw_invoices.completed_at, 'UTC', :timeZone4)) BETWEEN :beginDate1 AND :endDate1
							ELSE DATE(CONVERT_TZ(raw_invoices.created_at, 'UTC', :timeZone5)) BETWEEN :beginDate2 AND :endDate2
						END
					GROUP BY raw_invoice_items.id
				) AS invoice_items
				INNER JOIN tblOrganizationLocations AS locations
				  ON invoice_items.location_id = locations.id
				INNER JOIN tblUsers AS users
				  ON invoice_items.invoice_user_id = users.id
				INNER JOIN tblPatientOwnerInformation AS owners
				  ON invoice_items.invoice_owner_id = owners.id
				INNER JOIN tblClients AS clients
				  ON invoice_items.invoice_patient_id = clients.id
				GROUP BY invoice_items.invoice_id
				ORDER BY invoice_items.invoice_date
				",
			),
			$this->getQueryParameters(),
		);

		return response()->json($results);
	}

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
			"timeZone5" => $request->input("timeZone"),
		];
	}
}
