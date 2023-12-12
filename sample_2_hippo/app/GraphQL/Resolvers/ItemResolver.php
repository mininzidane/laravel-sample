<?php

namespace App\GraphQL\Resolvers;

use Closure;

class ItemResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (
			isset($args["category"]) &&
			!empty($args["category"]) &&
			$args["category"] != "null"
		) {
			$query->join(
				"item_categories",
				"item_categories.id",
				"=",
				"items.category_id",
			);
			$query->where("item_categories.id", "=", $args["category"]);
		}

		if (
			isset($args["typeId"]) &&
			!empty($args["typeId"]) &&
			$args["typeId"] != "null"
		) {
			$query->join("item_types", "item_types.id", "=", "items.type_id");
			$query->where("item_types.id", "=", $args["typeId"]);
		}

		if (isset($args["upcNumber"]) && $args["upcNumber"]) {
			$query->where(
				"items.number",
				"like",
				"%" . $args["upcNumber"] . "%",
			);
		}

		if (isset($args["location"]) && $args["location"]) {
			$query->join("item_locations", function ($join) {
				$join
					->on("item_locations.item_id", "=", "items.id")
					->whereNull("item_locations.deleted_at");
			});
			$query->where("item_locations.location_id", "=", $args["location"]);
		}

		if (isset($args["isVaccine"]) && $args["isVaccine"]) {
			$query->where("items.is_vaccine", $args["isVaccine"]);
		}

		if (isset($args["isSalesRegister"]) && $args["isSalesRegister"]) {
			$query->where("items.hide_from_register", false);
		}

		if (isset($args["checkoutSearch"]) && $args["checkoutSearch"]) {
			$searchFor = $args["checkoutSearch"];
		}

		if (isset($args["receivingSearch"]) && $args["receivingSearch"]) {
			$searchFor = $args["receivingSearch"];
			$query->whereIn("items.type_id", [2, 7]);
		}

		if (isset($searchFor)) {
			$query->where(function ($query) use ($searchFor) {
				$query->where("items.number", "like", "%" . $searchFor . "%");
				$query->orWhere("items.name", "like", "%" . $searchFor . "%");
			});
		}

		return $query;
	}
}
