<?php

namespace App\GraphQL\Resolvers;

use Closure;

class InvoiceItemResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();
		$query->join(
			"invoices",
			"invoices.id",
			"=",
			"invoice_items.invoice_id",
		);

		if (
			isset($args["ownerId"]) &&
			!empty($args["ownerId"]) &&
			$args["ownerId"] != "null"
		) {
			$query->where("invoices.owner_id", $args["ownerId"]);
		}

		if (
			isset($args["locationId"]) &&
			!empty($args["locationId"]) &&
			$args["locationId"] != "null"
		) {
			$query->where("invoices.location_id", $args["locationId"]);
		}

		if (
			isset($args["categoryId"]) &&
			!empty($args["categoryId"]) &&
			$args["categoryId"] != "null"
		) {
			$query->where("invoice_items.category_id", $args["categoryId"]);
		}

		if (
			isset($args["typeId"]) &&
			!empty($args["typeId"]) &&
			$args["typeId"] != "null"
		) {
			$query->where("invoice_items.type_id", $args["typeId"]);
		}

		if (
			isset($args["invoiceStatus"]) &&
			!empty($args["invoiceStatus"]) &&
			$args["invoiceStatus"] != "null"
		) {
			$query->where("invoices.status_id", $args["invoiceStatus"]);
		}

		if (
			isset($args["name"]) &&
			!empty($args["name"]) &&
			$args["name"] != "null"
		) {
			$query->join(
				"tblClients",
				"tblClients.id",
				"=",
				"invoices.patient_id",
			);
			$query->where(
				"tblClients.first_name",
				"LIKE",
				"%" . $args["name"] . "%",
			);
			$query->orWhere(
				"tblClients.last_name",
				"LIKE",
				"%" . $args["name"] . "%",
			);
		}

		return $query;
	}
}
