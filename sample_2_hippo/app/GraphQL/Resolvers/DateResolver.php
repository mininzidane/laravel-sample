<?php

namespace App\GraphQL\Resolvers;

use Closure;

class DateResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["startDate"]) && isset($args["endDate"])) {
			$query->between($args["startDate"], $args["endDate"]);
		} elseif (isset($args["endDate"])) {
			$query->before($args["endDate"]);
		} elseif (isset($args["startDate"])) {
			$query->after($args["startDate"]);
		}

		return $query;
	}
}
