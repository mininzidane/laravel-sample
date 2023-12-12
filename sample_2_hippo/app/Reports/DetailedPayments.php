<?php

namespace App\Reports;

use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailedPayments extends ReportModel
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
		$results = DB::connection($this->getConnectionName())->select(
			DB::raw("
				SELECT
					invoices.id as invoice_id,
					DATE_FORMAT(payments.received_at, '%m-%d-%Y') AS received_at,
				    payments.id AS payment_id,
					payment_methods.name as payment_method,
					clearent_transactions.card_type AS card_type,
					clearent_transactions.last_four_digits AS last_four_digits,
					CONCAT(locations.name, ' (', invoices.location_id, ')') as location_name,
					CONCAT(owners.first_name, ' ', owners.last_name) as owner_name,
					DATE_FORMAT(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1), '%m-%d-%Y') AS invoice_created_at,
					DATE_FORMAT(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2), '%m-%d-%Y') AS invoice_completed_at,
					invoice_payments.amount_applied as amount,
					invoice_statuses.name as sale_status,
					invoices.location_id AS location_id
				FROM invoice_payments
				INNER JOIN payments
					ON invoice_payments.payment_id = payments.id
				INNER JOIN payment_methods
					ON payments.payment_method_id = payment_methods.id
				LEFT JOIN clearent_transactions
					ON payments.id = clearent_transactions.payment_id
				INNER JOIN invoices
					ON invoice_payments.invoice_id = invoices.id
				INNER JOIN invoice_statuses
					ON invoices.status_id = invoice_statuses.id
				INNER JOIN tblOrganizationLocations AS locations
					ON invoices.location_id = locations.id
				LEFT JOIN tblPatientOwnerInformation AS owners
					ON invoices.owner_id = owners.id
				WHERE
				    CASE :saleStatus
				        WHEN 1 THEN invoices.status_id = 1
				        WHEN 2 THEN invoices.status_id = 1 OR invoices.status_id = 2
				        WHEN 3 THEN invoices.status_id = 2
				        WHEN 4 THEN invoices.status_id = 3
				    END
					AND payments.deleted_at IS NULL
					AND FIND_IN_SET(location_id, :locations)
					AND payments.received_at BETWEEN :beginDate AND :endDate
				ORDER BY payment_id, received_at
                "),
			$this->getQueryParameters(),
		);
		// inconsistency in CASE between :saleStatus and invoices.status_id caused by the store being shared by multiple reports

		if ($this->format === static::FORMAT_PDF) {
			$locationNames = Location::on($this->getConnectionName())
				->select("name")
				->whereIn(
					"id",
					explode(",", $this->getQueryParameters()["locations"]),
				)
				->get();

			$grouped_results = [];
			foreach ($results as $result) {
				$grouped_results[$result->payment_method][] = $result;
			}

			return response()->json([
				"payment_methods" => $grouped_results,
				"locationNames" => $locationNames,
				"beginDate" => $this->getQueryParameters()["beginDate"],
				"endDate" => $this->getQueryParameters()["endDate"],
			]);
		} else {
			return response()->json($results);
		}
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
			"saleStatus" => $request->input("saleStatus"),
			"timeZone1" => $request->input("timeZone"),
			"timeZone2" => $request->input("timeZone"),
		];
	}
}
