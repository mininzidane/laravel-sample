<?php

namespace App\GraphQL\Resolvers;

use Closure;

class TreatmentResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["invoice"])) {
			$query->where("sale_id", $args["invoice"]);
		}

		if (isset($args["appointment"])) {
			$query->where("schedule_event_id", $args["appointment"]);
		}

		if (isset($args["removed"])) {
			$query->where("removed", $args["removed"]);
		}

		if (isset($args["rejected"])) {
			$query->where("rejected", $args["rejected"]);
		}

		if (isset($args["completed"])) {
			$query->where("completed", $args["completed"]);
		}

		return $query;
	}
}
