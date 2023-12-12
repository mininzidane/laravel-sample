<?php

namespace App\GraphQL\Resolvers;

use Closure;

class PhoneResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["phone"])) {
			$query->phoneLike($args["phone"]);
		}

		return $query;
	}
}
