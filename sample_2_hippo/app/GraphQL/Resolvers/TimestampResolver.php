<?php

namespace App\GraphQL\Resolvers;

use Closure;

class TimestampResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["since"])) {
			$query->after($args["since"]);
		}

		return $query;
	}
}
