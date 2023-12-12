<?php

namespace App\Reports;

use App\Models\Item;
use App\Models\ItemType;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemKitList extends ReportModel
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
		$locations = array_map(function ($location) {
			$locationName = Location::on($this->getConnectionName())
				->select("name")
				->where("id", $location)
				->firstOrFail()->name;

			$itemKits = DB::connection($this->getConnectionName())
				->table("items")
				->where("type_id", "=", ItemType::ITEM_KIT)
				->select(["items.id", "name", "description"])
				->join(
					"item_locations",
					"item_locations.item_id",
					"=",
					"items.id",
				)
				->where("location_id", $location)
				->whereNull("items.deleted_at")
				->whereNull("item_locations.deleted_at")
				->get()
				->map(function ($result) use ($location) {
					$result->items = DB::connection($this->getConnectionName())
						->table("items")
						->join(
							"item_locations",
							"item_locations.item_id",
							"=",
							"items.id",
						)
						->join(
							"item_kit_items",
							"item_kit_items.item_id",
							"=",
							"items.id",
						)
						->select([
							"items.id",
							"items.number",
							"items.name",
							"items.cost_price",
							"items.unit_price",
							"item_kit_items.quantity",
							"items.deleted_at",
						])
						->where("item_locations.location_id", $location)
						->where("item_kit_items.item_kit_id", $result->id)
						->whereNull("item_locations.deleted_at")
						->whereNull("item_kit_items.deleted_at")
						->get();
					return $result;
				});

			return [
				"name" => $locationName,
				"itemKits" => $itemKits,
			];
		}, $this->getQueryParameters()["locations"]);

		return response()->json(["locations" => $locations]);
	}
}
