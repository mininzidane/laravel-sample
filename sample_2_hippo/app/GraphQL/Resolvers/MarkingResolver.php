<?php

namespace App\GraphQL\Resolvers;

use Closure;

class MarkingResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (
			isset($args["species"]) &&
			!empty($args["species"]) &&
			$args["species"] != "null"
		) {
			$query->where("tblMarkings.species", "=", $args["species"]);
		}

		return $query;
	}
}
