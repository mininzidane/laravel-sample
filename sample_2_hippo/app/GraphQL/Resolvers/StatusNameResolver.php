<?php

namespace App\GraphQL\Resolvers;

use Closure;

class StatusNameResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["name"])) {
			$query->where("status_name", "=", $args["name"]);
		}

		return $query;
	}
}
