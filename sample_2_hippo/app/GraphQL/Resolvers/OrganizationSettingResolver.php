<?php

namespace App\GraphQL\Resolvers;

use Closure;

class OrganizationSettingResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["settingName"])) {
			$query->where("setting_name", $args["settingName"]);
		}

		return $query;
	}
}
