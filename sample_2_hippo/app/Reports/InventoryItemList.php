<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryItemList extends ReportModel
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
		$inventoryItemList = DB::connection($this->getConnectionName())->select(
			DB::raw("
                SELECT items.id AS item_id,
                    items.name AS item_name,
                    item_categories.name AS category_name,
                    item_types.name AS item_type_name,
                    items.number AS item_number,
                    items.description AS item_description,
                    IF(items.deleted_at IS NULL, 'N', 'Y') AS item_is_deleted,
                    items.cost_price AS item_cost_price,
                    items.unit_price AS item_unit_price,
                    items.minimum_sale_amount AS item_minimum_sale_amount,
                    items.markup_percentage AS item_markup_percentage,
                    items.dispensing_fee AS item_dispensing_fee,
                    items.is_non_taxable AS item_is_non_taxable,
                    items.allow_alt_description AS item_allow_alt_description,
                    items.is_single_line_kit AS item_is_single_line_kit,
                    IF(items.is_controlled_substance = 1, 'Y', 'N') AS item_is_controlled_substance,
                    IF(items.is_euthanasia = 1, 'Y', 'N') AS item_is_euthanasia,
                    items.is_in_wellness_plan AS item_is_in_wellness_plan,
                    IF(items.is_prescription = 1, 'Y', 'N') AS item_is_prescription,
                    IF(items.is_reproductive = 1, 'Y', 'N') AS item_is_reproductive,
                    items.is_serialized AS item_is_serialized,
                    IF(items.is_vaccine = 1, 'Y', 'N') AS item_is_vaccine,
                    item_locations.location_id,
                    tblOrganizationLocations.name AS location_name
                FROM items
                INNER JOIN item_types
                    ON items.type_id = item_types.id
                LEFT JOIN item_categories
                    ON items.category_id = item_categories.id
                LEFT JOIN item_locations
                    ON items.id = item_locations.item_id
                LEFT JOIN tblOrganizationLocations
                    ON item_locations.location_id = tblOrganizationLocations.id
				WHERE
                    items.deleted_at IS NULL
                    AND FIND_IN_SET(item_locations.location_id, :locations)
            "),
			$this->getQueryParameters(),
		);

		return response()->json($inventoryItemList);
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
		];
	}
}
