<?php

namespace App\GraphQL\Resolvers;

use Closure;

class ClearentTransactionResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["clearentTerminalId"])) {
			$query->where("clearent_terminal_id", $args["clearentTerminalId"]);
		}

		return $query;
	}
}
