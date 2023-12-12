<?php

namespace App\GraphQL\Resolvers;

use Closure;

class UserResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$this->args = $this->resolver->getArgs();

		if (isset($this->args["email"]) && $this->args["email"]) {
			$query->where("email", "like", "%" . $this->args["email"] . "%");
		}

		if (isset($this->args["isProvider"]) && $this->args["isProvider"]) {
			$query->where("degree", "!=", "")->whereNotNull("degree");
		}

		if (isset($this->args["username"]) && $this->args["username"]) {
			$query->where(
				"username",
				"like",
				"%" . $this->args["username"] . "%",
			);
		}

		if (isset($this->args["firstName"]) && $this->args["firstName"]) {
			$query
				->where(
					"first_name",
					"like",
					"%" . $this->args["firstName"] . "%",
				)
				->orWhere(
					"last_name",
					"like",
					"%" . $this->args["lastName"] . "%",
				);
		}
		if (isset($this->args["location"]) && $this->args["location"]) {
			$query->join("tblUserLocations", function ($join) {
				$join
					->on("tblUsers.id", "=", "tblUserLocations.user_id")
					->where(
						"tblUserLocations.location_id",
						"=",
						$this->args["location"],
					);
			});
		}
		if (isset($this->args["specialty"]) && $this->args["specialty"]) {
			$query->where(
				"specialty",
				"like",
				"%" . $this->args["specialty"] . "%",
			);
		}
		if (isset($this->args["degree"]) && $this->args["degree"]) {
			$query->where("degree", "like", "%" . $this->args["degree"] . "%");
		}
		if (isset($this->args["role"]) && $this->args["role"]) {
			$query->join("tblUserAccessLevels", function ($join) {
				$join->on("tblUsers.id", "=", "tblUserAccessLevels.user_id");
			});
			$query->join("tblAccessLevels", function ($join) {
				$join->on(
					"tblUserAccessLevels.access_level",
					"=",
					"tblAccessLevels.al",
				);
			});
			$query->join("roles", function ($join) {
				$join->on("tblAccessLevels.access_level", "=", "roles.name");
			});
			$query->where("roles.id", "=", $this->args["role"]);
		}
		if (isset($this->args["active"]) && $this->args["active"] === true) {
			$query->where("active", 1);
		}
		if (
			isset($this->args["emailVerified"]) &&
			$this->args["emailVerified"] === true
		) {
			$query->where("email_verified", 1);
		}
		return $query;
	}
}
