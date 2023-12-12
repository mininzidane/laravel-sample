<?php

namespace App\GraphQL\Resolvers;

use Closure;

class NameResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["name"])) {
			$query->nameLike($args["name"]);
		}

		return $query;
	}
}
