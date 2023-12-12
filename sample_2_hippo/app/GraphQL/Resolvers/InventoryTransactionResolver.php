<?php

namespace App\GraphQL\Resolvers;

use Closure;

class InventoryTransactionResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		$query->join(
			"inventory",
			"inventory_transactions.inventory_id",
			"=",
			"inventory.id",
		);

		if (
			isset($args["itemId"]) &&
			!empty($args["itemId"]) &&
			$args["itemId"] != "null"
		) {
			$query->where("inventory.item_id", $args["itemId"]);
		}

		if (
			isset($args["locationId"]) &&
			!empty($args["locationId"]) &&
			$args["locationId"] != "null"
		) {
			$query->where("inventory.location_id", $args["locationId"]);
		}

		if (
			isset($args["userId"]) &&
			!empty($args["userId"]) &&
			$args["userId"] != "null"
		) {
			$query->where("inventory_transactions.user_id", $args["userId"]);
		}
		if (
			isset($args["statusId"]) &&
			!empty($args["statusId"]) &&
			$args["statusId"] != "null"
		) {
			$query->where(
				"inventory_transactions.status_id",
				$args["statusId"],
			);
		}

		return $query;
	}
}
