<?php

namespace App\GraphQL\Resolvers;

use Closure;

class ClearentTokenResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["owner"])) {
			$query->where("owner_id", $args["owner"]);
		}

		return $query;
	}
}
