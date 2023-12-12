<?php

namespace App\GraphQL\Resolvers;

use Closure;

class LogActionResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (
			isset($args["action"]) &&
			!empty($args["action"]) &&
			$args["action"] != "null"
		) {
			$query->where("action", "like", "%" . $args["action"] . "%");
		}

		return $query;
	}
}
