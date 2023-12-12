<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryExpirationList extends ReportModel
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
			DB::raw("
                SELECT
					CONCAT(locations.name, ' (', inventory.location_id, ')') as location_name,
					items.id AS item_id,
					inventory.lot_number AS inventory_lot_number,
					items.name AS item_name,
					item_categories.name AS item_category,
					items.cost_price AS item_cost_price,
					items.unit_price AS item_retail_price,
					items.minimum_on_hand AS reorder_level,
					DATE_FORMAT(inventory.expiration_date, '%m-%d-%Y') as expiration_date,
					SUM(inventory.remaining_quantity) AS remaining_quantity
				FROM inventory
				INNER JOIN tblOrganizationLocations AS locations
					ON inventory.location_id = locations.id
				INNER JOIN items AS items
					ON inventory.item_id = items.id
				LEFT JOIN item_categories AS item_categories
					ON items.category_id = item_categories.id
				WHERE inventory.deleted_at IS NULL
					AND inventory.status_id = 3
					AND IF(DATE_FORMAT(inventory.expiration_date, '%m-%d-%Y') = '01-01-1970', NULL, inventory.expiration_date) < CURDATE() + INTERVAL 60 DAY
					AND inventory.remaining_quantity != 0
					AND FIND_IN_SET(inventory.location_id, :locations)  
				    AND items.deleted_at is null
				GROUP BY
				  location_name,
				  item_id,
				  inventory_lot_number,
				  item_category,
				  item_cost_price,
				  item_retail_price,
				  reorder_level,
				  expiration_date
				HAVING remaining_quantity > 0
				  AND expiration_date IS NOT NULL
				ORDER BY expiration_date ASC;
            "),
			$this->getQueryParameters(),
		);

		return response()->json($results);
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
		];
	}
}
