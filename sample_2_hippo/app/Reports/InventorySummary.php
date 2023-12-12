<?php

namespace App\Reports;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventorySummary extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters([
			"locations" => $request->input("locations"),
		]);
		parent::__construct();
	}

	public function generateReportData()
	{
		return response()->json([
			"locations" => array_map(function ($location) {
				$inventory = DB::connection($this->getConnectionName())
					->table("inventory")
					->join("items", "items.id", "=", "inventory.item_id")
					->leftJoin(
						"item_categories",
						"item_categories.id",
						"=",
						"items.category_id",
					)
					->join("item_types", "item_types.id", "=", "items.type_id")
					->distinct("items.id")
					->select([
						"items.id as item_id",
						"items.name as item_name",
						"items.number as item_number",
						"item_categories.name as category",
					])
					->where("item_types.process_inventory", "=", 1)
					->where("inventory.location_id", "=", $location)
					->where("inventory.remaining_quantity", ">", 0)
					->whereNull("inventory.deleted_at")
					->whereNull("items.deleted_at")
					->get()
					->map(function ($record) use ($location) {
						$record->lots = DB::connection(
							$this->getConnectionName(),
						)
							->table("inventory")
							->leftJoin(
								"receiving_items",
								"receiving_items.id",
								"=",
								"inventory.receiving_item_id",
							)
							->leftJoin(
								"tblOrganizationLocations",
								"tblOrganizationLocations.id",
								"=",
								"inventory.location_id",
							)
							->leftJoin(
								"tblOrganizations",
								"tblOrganizations.id",
								"=",
								"tblOrganizationLocations.organization_id",
							)
							->leftJoin(
								"items",
								"items.id",
								"=",
								"inventory.item_id",
							)
							->select([
								"tblOrganizations.currency_symbol",
								"inventory.lot_number",
								"inventory.expiration_date",
								"inventory.remaining_quantity",
								DB::raw(
									"coalesce(receiving_items.cost_price,items.cost_price) as cost_price",
								),
								DB::raw(
									"coalesce(receiving_items.unit_price,items.unit_price) as unit_price",
								),
							])
							->where("inventory.item_id", "=", $record->item_id)
							->where("inventory.remaining_quantity", ">", 0)
							->where("inventory.location_id", "=", $location)
							->whereNull("inventory.deleted_at")
							->whereNull("receiving_items.deleted_at")
							->get();
						return $record;
					});
				return [
					"name" => Location::on($this->getConnectionName())
						->select(["name"])
						->where("id", $location)
						->firstOrFail()->name,
					"inventory" => $inventory,
				];
			}, $this->getQueryParameters()["locations"]),
		]);
	}
}
