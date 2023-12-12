<?php

namespace App\GraphQL\Resolvers;

use Closure;

class ActiveResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["active"])) {
			$query->where("active", "=", $args["active"]);
		}

		return $query;
	}
}
