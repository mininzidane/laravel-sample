<?php

namespace App\GraphQL\Resolvers;

use Closure;

class EmailResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["email"])) {
			$query->emailLike($args["email"]);
		}

		return $query;
	}
}
