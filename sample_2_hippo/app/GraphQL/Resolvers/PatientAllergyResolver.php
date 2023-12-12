<?php

namespace App\GraphQL\Resolvers;

use Closure;

class PatientAllergyResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["clientId"]) && $args["clientId"]) {
			$query->where("client_id", "=", $args["clientId"]);
		}

		if (isset($args["removed"]) && $args["removed"]) {
			$query->where("removed", "=", $args["removed"]);
		}

		return $query;
	}
}
