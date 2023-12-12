<?php

namespace App\GraphQL\Resolvers;

use Closure;

class AppointmentResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["blocked"])) {
			$query->where("blocked", $args["blocked"]);
		}

		return $query;
	}
}
