<?php

namespace App\GraphQL\Resolvers;

use Closure;

class BreedResolver extends HippoResolver
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
			$query->where("tblBreeds.species", "=", $args["species"]);
		}

		return $query;
	}
}
