<?php

namespace App\GraphQL\Resolvers;

use Closure;

class CreditResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["type"])) {
			$query->where("type", $args["type"]);
		}

		if (isset($args["owner"])) {
			$query->where("owner_id", $args["owner"]);
		}

		if (
			isset($args["ownerName"]) &&
			!empty($args["ownerName"]) &&
			$args["ownerName"] != "null"
		) {
			$query->join(
				"tblPatientOwnerInformation",
				"tblPatientOwnerInformation.id",
				"=",
				"credits.owner_id",
			);

			$item = $args["ownerName"];

			//when using raw statements in laravel make sure to bind them
			$query->where(function ($query) use ($item) {
				$query->whereRaw(
					'concat(trim(tblPatientOwnerInformation.first_name), " " , trim(tblPatientOwnerInformation.last_name)) like :item',
					["item" => "%" . $item . "%"],
				);
			});
		}

		if (isset($args["number"])) {
			$query->whereLike("number", $args["number"]);
		}

		//If we are looking for an exact match try it now, can't find it return nothing
		if (
			isset($args["exactMatch"]) &&
			$args["exactMatch"] &&
			isset($args["number"])
		) {
			$query->where("number", $args["number"]);
		}

		return $query;
	}
}
