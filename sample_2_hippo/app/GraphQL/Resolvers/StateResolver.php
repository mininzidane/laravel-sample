<?php

namespace App\GraphQL\Resolvers;

use Closure;

class StateResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["iso"]) && $args["iso"]) {
			$query->where("iso", "=", $args["iso"]);
		}
		return $query;
	}
}
