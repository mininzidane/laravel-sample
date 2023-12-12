<?php

namespace App\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DetailedProviderProduction extends ReportModel
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
                    invoice_items.id AS item_id,
                    invoice_items.name AS item_name,
                    invoice_items.provider_id as provider_id,
                    invoice_items.is_prescription,
                    invoice_items.dispensing_fee,
                    invoice_items.discount_amount,
                    invoice_items.total AS item_total,
                    invoices.id AS invoice_id,
                    invoices.status_id,
					invoices.created_at AS invoice_created_at,
					invoices.completed_at AS invoice_completed_at,
                    item_types.id AS item_type_id,
					if(invoice_items.provider_id is null, 
						'Unassigned', 
						concat(users.first_name,' ', users.last_name)) AS provider_name,
                    users.first_name AS provider_first_name,
                    users.last_name AS provider_last_name,
                    patients.first_name AS patient_name,
                    owners.first_name AS owner_first_name,
                    owners.last_name AS owner_last_name,
                    locations.id as location_id,
                    locations.name AS location_name
                FROM invoice_items
                INNER JOIN item_types
                    ON invoice_items.type_id = item_types.id
                    AND item_types.deleted_at IS NULL
                LEFT JOIN tblUsers AS users
                    ON invoice_items.provider_id = users.id
                    AND users.deleted_at IS NULL
                INNER JOIN invoices
                    ON invoice_items.invoice_id = invoices.id
                    AND invoices.deleted_at IS NULL
                INNER JOIN tblClients AS patients
                    ON invoices.patient_id = patients.id
                INNER JOIN tblPatientOwnerInformation AS owners
                    ON invoices.owner_id = owners.id
                INNER JOIN tblOrganizationLocations AS locations
                    ON invoices.location_id = locations.id
                WHERE invoice_items.deleted_at IS NULL
                    AND invoices.status_id IN (1,2)
                    AND FIND_IN_SET(locations.id, :locations)
                    AND (
                        FIND_IN_SET(invoice_items.provider_id, :providers)
                        OR
                        invoice_items.provider_id IS NULL
                        )
					AND CASE
						WHEN (invoices.status_id = 1) THEN (DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1)) BETWEEN :beginDate1 AND :endDate1)
						WHEN (invoices.status_id = 2) THEN (DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2)) BETWEEN :beginDate2 AND :endDate2)
					END
                ORDER BY isnull(provider_last_name), provider_first_name, location_name, 
                        invoices.status_id DESC, invoice_items.created_at ASC
            "),
			$this->getQueryParameters(),
		);
		$provider_location_totals = DB::connection(
			$this->getConnectionName(),
		)->select(
			DB::raw("
				SELECT
					if(invoice_items.provider_id is null, 
						'Unassigned', 
						concat(users.first_name,' ', users.last_name)) AS provider_name,
					locations.name AS location_name,
					if(invoices.status_id = 1, 'Open', 'Collected') as invoice_status,
					round(sum(if(invoice_items.type_id = 2 AND invoice_items.is_prescription = 0, 
					invoice_items.total - invoice_items.dispensing_fee, 0)), 2) as stocking,  
					round(sum(if(invoice_items.is_prescription = 1, 
					invoice_items.total - invoice_items.dispensing_fee, 0)), 2) as prescriptions,
					round(sum(if(invoice_items.type_id = 3 AND invoice_items.is_prescription = 0, 
					invoice_items.total - invoice_items.dispensing_fee, 0)), 2) as procedures,  
					round(sum(if(invoice_items.is_prescription = 0 AND (invoice_items.type_id != 2 AND invoice_items.type_id != 3), 
					invoice_items.total - invoice_items.dispensing_fee, 0)), 2) as other,
					round(sum(invoice_items.dispensing_fee), 2) as dispensing,
					round(sum(invoice_items.discount_amount), 2) as discounts,
					round(sum(invoice_items.total), 2) as total
				FROM
					invoice_items
					INNER JOIN item_types
						ON invoice_items.type_id = item_types.id
						AND item_types.deleted_at IS NULL
					LEFT JOIN tblUsers AS users
						ON invoice_items.provider_id = users.id
						AND users.deleted_at IS NULL					
					INNER JOIN invoices 
						ON invoice_items.invoice_id = invoices.id 
						AND invoices.deleted_at IS NULL
					INNER JOIN invoice_statuses
						ON invoice_statuses.id = invoices.status_id 
						AND invoice_statuses.deleted_at is NULL   
					INNER JOIN tblOrganizationLocations AS locations
						ON invoices.location_id = locations.id
				WHERE
					invoice_items.deleted_at IS NULL
					AND invoices.status_id IN (1,2)
					AND FIND_IN_SET(locations.id, :locations)
					AND (
						FIND_IN_SET(invoice_items.provider_id, :providers)
						OR
						invoice_items.provider_id IS NULL
						)
					AND CASE
						WHEN (invoices.status_id = 1) THEN (DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1)) BETWEEN :beginDate1 AND :endDate1)
						WHEN (invoices.status_id = 2) THEN (DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2)) BETWEEN :beginDate2 AND :endDate2)
					END
				group by invoice_items.provider_id, invoices.location_id, invoices.status_id
				ORDER BY invoice_items.provider_id, invoices.location_id, invoices.status_id asc
			"),
			$this->getQueryParameters(),
		);

		$report_data = $this->buildReportData(
			json_decode(json_encode($results), true),
		);
		$totals = $this->calculateTotals(
			json_decode(json_encode($provider_location_totals), true),
		);

		return response()->json([
			"beginDate1" => $this->getQueryParameters()["beginDate1"],
			"endDate1" => $this->getQueryParameters()["endDate1"],
			"providers" => $report_data,
			"totals" => $totals["totals"],
			"provider_location_totals" => $totals["provider_location_totals"],
			"grand_totals" => $totals["grand_totals"],
			"grand_total_totals" => $totals["grand_total_totals"],
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
			"providers" => implode(",", $request->input("providerIds")),
			"locations" => implode(",", $request->input("locations")),
		];
	}

	// Coerce data from query into format needed for Blade template
	private function buildReportData($results)
	{
		$providers = [];

		foreach ($results as $result) {
			// SQL limits statuses to 1 or 2
			$providers[$result["provider_name"]][$result["location_name"]][
				$result["status_id"] === 1 ? "Open" : "Collected"
			][] = $result;
		}

		return $providers;
	}

	private function calculateTotals($rawTotals)
	{
		$totals = [];
		$grand_totals = [];
		$grand_total_totals = [];
		$provider_location_totals = [];

		$item_types = [
			"stocking" => 0,
			"prescriptions" => 0,
			"procedures" => 0,
			"other" => 0,
			"dispensing" => 0,
			"discounts" => 0,
			"total" => 0,
		];

		// Initialize totals buckets
		foreach ($rawTotals as $total) {
			$totals[$total["provider_name"]][$total["location_name"]] = [
				"Collected" => $item_types,
				"Open" => $item_types,
			];

			$provider_location_totals[$total["provider_name"]][
				$total["location_name"]
			] = $item_types;

			$grand_totals[$total["provider_name"]] = [
				"Collected" => $item_types,
				"Open" => $item_types,
			];

			$grand_total_totals[$total["provider_name"]] = $item_types;
		}

		foreach ($rawTotals as $total) {
			$totals[$total["provider_name"]][$total["location_name"]][
				$total["invoice_status"]
			] = [
				"stocking" => $total["stocking"],
				"prescriptions" => $total["prescriptions"],
				"procedures" => $total["procedures"],
				"other" => $total["other"],
				"dispensing" => $total["dispensing"],
				"discounts" => $total["discounts"],
				"total" => $total["total"],
			];
		}

		// Calculate provider per location totals
		foreach ($totals as $provider => $locations) {
			foreach ($locations as $location => $data) {
				foreach (array_keys($item_types) as $item_type) {
					$provider_location_totals[$provider][$location][
						$item_type
					] =
						$data["Collected"][$item_type] +
						$data["Open"][$item_type];
				}
			}
		}

		// Calculate provider all-location totals and grand totals
		foreach ($totals as $provider => $locations) {
			foreach ($locations as $location) {
				foreach (array_keys($item_types) as $item_type) {
					$grand_totals[$provider]["Collected"][$item_type] +=
						$location["Collected"][$item_type];
					$grand_totals[$provider]["Open"][$item_type] +=
						$location["Open"][$item_type];
					$grand_total_totals[$provider][$item_type] +=
						$location["Collected"][$item_type] +
						$location["Open"][$item_type];
				}
			}
		}

		return [
			"totals" => $totals,
			"provider_location_totals" => $provider_location_totals,
			"grand_totals" => $grand_totals,
			"grand_total_totals" => $grand_total_totals,
		];
	}
}
