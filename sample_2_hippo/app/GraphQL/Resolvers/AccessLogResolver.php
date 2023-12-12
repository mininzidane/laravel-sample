<?php

namespace App\GraphQL\Resolvers;

use Closure;

class AccessLogResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (
			isset($args["actionId"]) &&
			!empty($args["actionId"]) &&
			$args["actionId"] != "null"
		) {
			$query->join(
				"tblLogActions",
				"tblLogActions.id",
				"=",
				"tblLog.action_id",
			);
			$query->where("tblLogActions.id", "=", $args["actionId"]);
		}

		if (
			isset($args["userId"]) &&
			!empty($args["userId"]) &&
			$args["userId"] != "null"
		) {
			$query->join("tblUsers", "tblUsers.id", "=", "tblLog.user_id");
			$query->where("tblUsers.id", "=", $args["userId"]);
		}

		$query->orderBy("timestamp", "desc");

		return $query;
	}
}
