<?php

namespace App\Reports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryList extends ReportModel
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

			$categories = DB::connection($this->getConnectionName())
				->table("item_categories")
				->select(["item_categories.id", "item_categories.name"])
				->whereNull("item_categories.deleted_at")
				->get()
				->map(function ($result) use ($location) {
					$result->count = DB::connection($this->getConnectionName())
						->table("items")
						->join(
							"item_locations",
							"item_locations.item_id",
							"=",
							"items.id",
						)
						->distinct("items.id")
						->where("items.category_id", "=", $result->id)
						->where("item_locations.location_id", $location)
						->whereNull("item_locations.deleted_at")
						->whereNull("items.deleted_at")
						->count();
					return $result;
				});

			return [
				"name" => $locationName,
				"categories" => $categories,
			];
		}, $this->getQueryParameters()["locations"]);

		return response()->json(["locations" => $locations]);
	}
}
