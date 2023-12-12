<?php

namespace App\GraphQL\Resolvers;

use Closure;

class SaleStatusResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["saleStatus"])) {
			$salesStatusCodes = explode(",", $args["saleStatus"]);

			$statusIntegers = [];

			foreach ($salesStatusCodes as $salesStatusCode) {
				switch (trim($salesStatusCode)) {
					case "ESTIMATE":
						$statusIntegers[] = 3;
						break;
					case "COMPLETE":
						$statusIntegers[] = 2;
						break;
					case "OPEN":
					default:
						$statusIntegers[] = 1;
						break;
				}
			}

			$query->whereIn("sale_status", $statusIntegers);
		}

		return $query;
	}
}
