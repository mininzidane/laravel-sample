<?php

namespace App\Reports;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryReceivings extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters(
			$this->buildParams($request),
		)->setReplicaConnection($request);

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$results = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"SELECT receiving_items.receiving_id AS receiving_id,
					   DATE(receiving_items.receiving_date) AS receiving_date,
					   COUNT(DISTINCT receiving_items.item_id) AS number_items_received,
					   ROUND(SUM(receiving_items.quantity), 2) AS quantity_received,
					   CONCAT(employee.first_name, ' ', employee.last_name) AS employee_name,
					   supplier.company_name AS supplier_name,
					   ROUND(SUM(receiving_items.total), 2) AS total,
					   ROUND(SUM(receiving_items.profit), 2) AS profit,
					   receiving_items.comment AS comment,
					   locations.name AS location_name
				FROM (SELECT raw_receivings.received_at AS receiving_date,
							 raw_receiving_items.receiving_id AS receiving_id,
							 raw_receivings.comment AS comment,
							 raw_receivings.user_id AS user_id,
							 raw_receiving_items.item_id AS item_id,
							 raw_receivings.supplier_id AS supplier_id,
							 raw_receiving_items.quantity AS quantity,
							 raw_receiving_items.cost_price AS cost_price,
							 raw_receiving_items.unit_price AS unit_price,
							 raw_receiving_items.discount_percentage AS discount_percentage,
							 raw_receiving_items.unit_price * raw_receiving_items.quantity - raw_receiving_items.unit_price * raw_receiving_items.quantity * raw_receiving_items.discount_percentage / 100 AS subtotal,
							 raw_receiving_items.line AS line,
							 raw_receiving_items.unit_price * raw_receiving_items.quantity - raw_receiving_items.unit_price * raw_receiving_items.quantity * raw_receiving_items.discount_percentage / 100 AS total,
							 (raw_receiving_items.unit_price * raw_receiving_items.quantity - raw_receiving_items.unit_price * raw_receiving_items.quantity * raw_receiving_items.discount_percentage / 100) - (raw_receiving_items.cost_price * raw_receiving_items.quantity) AS profit,
							 raw_receivings.location_id AS location_id
					 FROM receiving_items AS raw_receiving_items
					 INNER JOIN receivings AS raw_receivings
					   ON raw_receiving_items.receiving_id = raw_receivings.id
					 INNER JOIN items AS items
					   ON raw_receiving_items.item_id = items.id
					 WHERE received_at
						 BETWEEN :beginDate AND :endDate
					 GROUP BY raw_receivings.id,
							  raw_receiving_items.item_id,
							  raw_receiving_items.line) AS receiving_items
				INNER JOIN tblUsers AS employee
				  ON receiving_items.user_id = employee.id
				LEFT JOIN suppliers AS supplier
				  ON receiving_items.supplier_id = supplier.id
				INNER JOIN tblOrganizationLocations AS locations
				  ON receiving_items.location_id = locations.id
				WHERE FIND_IN_SET(receiving_items.location_id, :locations)
				GROUP BY receiving_items.receiving_id
				ORDER BY receiving_items.receiving_date",
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
			"endDate" => Carbon::create($request->input("endDate"))
				->addDay()
				->toDateString(),
		];
	}
}
