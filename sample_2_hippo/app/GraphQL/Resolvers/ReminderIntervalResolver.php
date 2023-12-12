<?php

namespace App\GraphQL\Resolvers;

use Closure;

class ReminderIntervalResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		$query->orderBy("reminder_intervals.name", "asc");

		return $query;
	}
}
