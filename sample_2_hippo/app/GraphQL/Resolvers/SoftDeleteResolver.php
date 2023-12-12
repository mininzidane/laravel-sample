<?php

namespace App\GraphQL\Resolvers;

use Closure;

class SoftDeleteResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["includeRemoved"]) && $args["includeRemoved"]) {
			$query->withTrashed();
		}

		if (isset($args["onlyRemoved"]) && $args["onlyRemoved"]) {
			$query->onlyTrashed();
		}

		return $query;
	}
}
