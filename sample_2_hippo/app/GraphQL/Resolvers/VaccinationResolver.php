<?php

namespace App\GraphQL\Resolvers;

use Closure;

class VaccinationResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		//$sort = $args['sort'] ?? '';

		if (isset($args["patient"]) && $args["patient"]) {
			$query->where("client_id", $args["patient"]);
		}

		if (isset($args["item"]) && $args["item"]) {
			$query->where("vaccine_item_id", $args["item"]);
		}

		return $query;
	}
}
