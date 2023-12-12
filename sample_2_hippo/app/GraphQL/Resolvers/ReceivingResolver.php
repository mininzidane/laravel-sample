<?php

namespace App\GraphQL\Resolvers;

use Closure;

class ReceivingResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["receivingStatus"]) && $args["receivingStatus"]) {
			$query->where("status_id", $args["receivingStatus"]);
		}

		if (isset($args["active"]) && $args["active"]) {
			$query->where("active", $args["active"]);
		}

		if (isset($args["supplier"]) && $args["supplier"]) {
			$query->where("supplier_id", $args["supplier"]);
		}

		if (isset($args["locationId"]) && $args["locationId"]) {
			$query->where("location_id", $args["locationId"]);
		}

		return $query;
	}
}
