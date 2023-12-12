<?php

namespace App\GraphQL\Resolvers;

use Closure;

class InvoiceAppliedDiscountResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["discountInvoiceItemId"])) {
			$query->where(
				"discount_invoice_item_id",
				$args["discountInvoiceItemId"],
			);
		}

		if (isset($args["adjustedInvoiceItemId"])) {
			$query->where(
				"adjusted_invoice_item_id",
				$args["adjustedInvoiceItemId"],
			);
		}

		return $query;
	}
}
