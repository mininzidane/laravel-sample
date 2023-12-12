<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailedCustomer extends ReportModel
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
					CONCAT(owners.last_name, ', ', owners.first_name, ' (', owners.id, ')') AS owner,
					CONCAT(patients.first_name, ' (', patients.id, ')') AS patient,
					invoice_details.invoice_id AS invoice_id,
					invoice_statuses.name AS status,
					CONVERT_TZ(invoice_details.invoice_date, 'UTC', :timezone1) AS invoice_date,
					invoice_details.invoice_line AS line,
					invoice_details.item_name AS item_name,
					invoice_details.quantity AS quantity,
					ROUND(invoice_details.subtotal ,2) AS subtotal,
					ROUND(invoice_details.total, 2) AS total,
					ROUND(invoice_details.tax, 2) AS tax,
					ROUND(invoice_details.profit, 2) AS profit,
					CONCAT(providers.first_name, ' ', providers.last_name) AS provider_name,
					locations.name AS location_name
				FROM (
					SELECT
						invoices.id AS invoice_id,
						invoices.status_id AS status_id,
						invoices.patient_id AS patient_id,
						invoices.owner_id AS owner_id,
						invoices.location_id AS location_id,
						CASE invoices.status_id
							WHEN 2 THEN invoices.completed_at
							ELSE invoices.created_at
						END AS invoice_date,
						invoice_items.id AS invoice_item_id,
						invoice_items.line AS invoice_line,
						invoice_items.item_id AS item_id,
						invoice_items.name AS item_name,
						invoice_items.quantity AS quantity,
						invoice_items.cost_price AS cost_price,
						invoice_items.unit_price AS unit_price,
						invoice_items.dispensing_fee AS dispensing_fee,
						invoice_items.total AS subtotal,
						invoice_items.total + SUM(IFNULL(invoice_item_taxes.amount, 0)) AS total,
						SUM(IFNULL(invoice_item_taxes.amount, 0)) AS tax,
						invoice_items.total - (invoice_items.cost_price * invoice_items.quantity) AS profit,
						invoice_items.provider_id AS provider_id
					FROM invoice_items AS invoice_items
					INNER JOIN invoices AS invoices
						ON invoice_items.invoice_id = invoices.id
						AND invoices.deleted_at IS NULL
					LEFT JOIN invoice_item_taxes AS invoice_item_taxes
						ON invoice_items.id = invoice_item_taxes.invoice_item_id
						AND invoice_item_taxes.deleted_at IS NULL
					WHERE invoice_items.deleted_at IS NULL
						AND FIND_IN_SET(invoices.location_id, :locations)
						AND CASE invoices.status_id
							WHEN 2 THEN DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timezone2)) BETWEEN :beginDate1 AND :endDate1
							ELSE DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timezone3)) BETWEEN :beginDate2 AND :endDate2
						END
						AND invoices.owner_id = :customerId
					    AND invoices.status_id = :saleStatus    
                        AND invoice_items.quantity " .
					$this->getSalesTypeSql() .
					" 0
					GROUP BY
						invoice_items.id) invoice_details
				INNER JOIN invoice_statuses AS invoice_statuses
					ON invoice_details.status_id = invoice_statuses.id
				INNER JOIN tblClients AS patients
					ON invoice_details.patient_id = patients.id
				INNER JOIN tblPatientOwnerInformation AS owners
					ON invoice_details.owner_id = owners.id
				LEFT JOIN tblUsers AS providers
					ON invoice_details.provider_id = providers.id
				INNER JOIN tblOrganizationLocations AS locations
					ON invoice_details.location_id = locations.id
				ORDER BY
					invoice_details.invoice_date,
					invoice_details.invoice_id,
					invoice_details.invoice_line",
			),
			$this->getQueryParameters(),
		);

		return response()->json($results);
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
			"timezone1" => $request->input("timeZone"),
			"timezone2" => $request->input("timeZone"),
			"timezone3" => $request->input("timeZone"),
			"beginDate1" => $request->input("beginDate"),
			"endDate1" => $request->input("endDate"),
			"beginDate2" => $request->input("beginDate"),
			"endDate2" => $request->input("endDate"),
			"customerId" => $request->input("detailId"),
			"saleStatus" => $request->input("saleStatus"),
		];
	}
}
