<?php

namespace App\Reports;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SurgeryCount extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request))->setFormat(
			$request,
		);
		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$location = Location::on($this->getConnectionName())
			->where("id", request()["locations"][0])
			->with("subregion", "organization", "tz")
			->first();

		// Total Procedure Count
		$grandTotal = DB::connection($this->getConnectionName())->selectOne(
			DB::raw("
					select round(ifnull(sum(quantity),0),1) as 'total_purchased' 
					from invoice_items
					inner join items on invoice_items.item_id = items.id and items.deleted_at is null
					inner join invoices on invoice_items.invoice_id = invoices.id and invoices.deleted_at is NULL
					where items.type_id = 3 -- Procedure
					  and invoices.status_id in (1, 2)
					  and invoices.location_id = :location1
					  and invoice_items.deleted_at is NULL
					  and case
						when (invoices.status_id = 1) THEN (DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1)) between :beginDate1 and :endDate1)
						when (invoices.status_id = 2) THEN (DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2)) between :beginDate2 and :endDate2)
					  end
				"),
			$this->getQueryParameters(),
		);

		// Avg. Procedure per Patient / Sale
		$averagePer = DB::connection($this->getConnectionName())->selectOne(
			DB::raw("
					select round(ifnull(avg(totalQuantity),0),2) as 'average_per'
					from (
						select sum(quantity) as 'totalQuantity'
						from invoice_items
						inner join items on invoice_items.item_id = items.id  and items.deleted_at is null
						inner join invoices on invoice_items.invoice_id = invoices.id and invoices.deleted_at is NULL
						where items.type_id = 3
						and invoices.status_id in (1, 2)
						and invoices.location_id = :location1
						and invoice_items.deleted_at is NULL
						and case
					  		when (invoices.status_id = 1) THEN (DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1)) between :beginDate1 and :endDate1)
							when (invoices.status_id = 2) THEN (DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2)) between :beginDate2 and :endDate2)
					  	end
						group by invoice_items.invoice_id
					) as raw"),
			$this->getQueryParameters(),
		);

		// Top 10 Procedures with counts
		$topProcedures = DB::connection($this->getConnectionName())->select(
			DB::raw("
					select invoice_items.name, round(sum(quantity),1) as 'total_purchased'
					from invoice_items
					inner join items on invoice_items.item_id = items.id and items.deleted_at is null
					inner join invoices on invoice_items.invoice_id = invoices.id and invoices.deleted_at is NULL
					where items.type_id = 3
					and invoices.status_id in (1, 2)
					and invoices.location_id = :location1
					and invoice_items.deleted_at is NULL
					and case
						when (invoices.status_id = 1) THEN (DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1)) between :beginDate1 and :endDate1)
						when (invoices.status_id = 2) THEN (DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2)) between :beginDate2 and :endDate2)
					end
					group by invoice_items.name
					ORDER BY sum(quantity) DESC, name
					LIMIT 10
				"),
			$this->getQueryParameters(),
		);

		// Procedure Count by Species
		$speciesProcedures = DB::connection($this->getConnectionName())->select(
			DB::raw("
					select species, invoice_items.name, round((sum(items.unit_price * quantity)) / sum(quantity),2) as avg_amount_per_procedure
					from invoice_items
					inner join invoices on invoice_items.invoice_id = invoices.id and invoices.deleted_at is NULL
					inner join items on invoice_items.item_id = items.id and items.deleted_at is null
					inner join tblClients on invoices.patient_id = tblClients.id and tblClients.deleted_at is null
					where items.type_id = 3
					and invoices.status_id in (1, 2)
					and invoices.location_id = :location1
					and invoice_items.deleted_at is NULL
					and items.unit_price > 0
					and case
						when (invoices.status_id = 1) THEN (DATE(CONVERT_TZ(invoices.created_at, 'UTC', :timeZone1)) between :beginDate1 and :endDate1)
						when (invoices.status_id = 2) THEN (DATE(CONVERT_TZ(invoices.completed_at, 'UTC', :timeZone2)) between :beginDate2 and :endDate2)
					end					
					group by species, invoice_items.name
					ORDER BY species, avg_amount_per_procedure DESC;
				"),
			$this->getQueryParameters(),
		);

		if ($this->format === static::FORMAT_PDF) {
			return response()->json([
				"location" => $location,
				"beginDate" => $this->getQueryParameters()["beginDate1"],
				"endDate" => $this->getQueryParameters()["endDate1"],
				"grandTotal" => $grandTotal,
				"averagePer" => $averagePer,
				"topProcedures" => $topProcedures ?? [],
				"speciesProcedures" => $this->buildData(
					$speciesProcedures ?? [],
				),
			]);
		} else {
			return response()->json($speciesProcedures);
		}
	}

	public function buildParams(Request $request): array
	{
		return [
			"location1" => $request->input("locations")[0],
			"timeZone1" => $request->input("timeZone"),
			"beginDate1" => $request->input("beginDate"),
			"endDate1" => $request->input("endDate"),
			"timeZone2" => $request->input("timeZone"),
			"beginDate2" => $request->input("beginDate"),
			"endDate2" => $request->input("endDate"),
		];
	}

	// Create an associative array using species as key
	private function buildData(array $queryResults): array
	{
		$data = [];

		$arrayData = json_decode(json_encode($queryResults), true);

		foreach ($arrayData as $row) {
			$data[$row["species"]][] = [
				"procedure" => $row["name"],
				"avg_amount" => $row["avg_amount_per_procedure"],
			];
		}

		return $data;
	}
}
