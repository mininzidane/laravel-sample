<?php

namespace App\GraphQL\Resolvers;

use Closure;

class LocationResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["locations"])) {
			$query->whereIn("location_id", explode(",", $args["locations"]));
		}

		if (isset($args["location_id"])) {
			$query->where("location_id", $args["location_id"]);
		}

		if (isset($args["name"])) {
			$query->where("name", "like", "%" . $args["name"] . "%");
		}

		if (isset($args["city"]) && $args["city"]) {
			$query->where("city", "like", "%" . $args["city"] . "%");
		}

		if (isset($args["zip"]) && $args["zip"]) {
			$query->where("zip", "like", "%" . $args["zip"] . "%");
		}

		if (isset($args["email"])) {
			$query->where("email", "like", "%" . $args["email"] . "%");
		}

		if (isset($args["subregion"]) && $args["subregion"] > 0) {
			$query->where("state", $args["subregion"]);
		}

		return $query;
	}
}
