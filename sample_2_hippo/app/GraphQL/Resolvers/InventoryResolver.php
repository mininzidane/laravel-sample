<?php

namespace App\GraphQL\Resolvers;

use Closure;

class InventoryResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (
			isset($args["itemId"]) &&
			!empty($args["itemId"]) &&
			$args["itemId"] != "null"
		) {
			$query->where("inventory.item_id", "=", $args["itemId"]);
		}

		if (
			isset($args["locationId"]) &&
			!empty($args["locationId"]) &&
			$args["locationId"] != "null"
		) {
			$query->where("inventory.location_id", "=", $args["locationId"]);
		}

		if (
			isset($args["statusId"]) &&
			!empty($args["statusId"]) &&
			$args["statusId"] != "null"
		) {
			$query->where("inventory.status_id", "=", $args["statusId"]);
		}

		if (
			isset($args["isOpen"]) &&
			!empty($args["isOpen"]) &&
			$args["isOpen"] == 1
		) {
			$query->where("inventory.is_open", "=", $args["isOpen"]);
		}

		return $query;
	}
}
