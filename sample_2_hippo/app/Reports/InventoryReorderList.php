<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReorderList extends ReportModel
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
					locations.id AS location_id,
					locations.name AS location_name,
					items.id AS item_id,
					items.name AS name,
					IFNULL(item_categories.name, 'Uncategorized') AS category,
					items.cost_price AS cost_price,
					items.unit_price AS unit_price,
					IFNULL(items.minimum_on_hand, 0) AS minimum_on_hand,
					SUM(IFNULL(inventory.remaining_quantity, 0)) AS remaining_quantity
				FROM items
				INNER JOIN item_locations
					ON items.id = item_locations.item_id
				INNER JOIN item_types
					ON items.type_id = item_types.id
				LEFT JOIN item_categories
					ON items.category_id = item_categories.id
				LEFT JOIN inventory
					ON items.id = inventory.item_id
					AND item_locations.location_id = inventory.location_id
					AND inventory.deleted_at IS NULL
				INNER JOIN tblOrganizationLocations AS locations
					ON item_locations.location_id = locations.id
				WHERE items.deleted_at IS NULL
					AND item_types.process_inventory = 1
					AND FIND_IN_SET(item_locations.location_id, :locations)
					AND items.minimum_on_hand IS NOT NULL
					AND items.minimum_on_hand > 0
				GROUP BY locations.id, items.id
				HAVING minimum_on_hand >= remaining_quantity
				ORDER BY locations.id, items.name;
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
