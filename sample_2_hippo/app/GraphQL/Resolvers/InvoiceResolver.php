<?php

namespace App\GraphQL\Resolvers;

use Closure;

class InvoiceResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		if (isset($args["invoiceStatus"]) && $args["invoiceStatus"]) {
			$query->where("status_id", $args["invoiceStatus"]);
		}

		if (isset($args["active"]) && $args["active"]) {
			$query->where("active", $args["active"]);
		}

		if (
			isset($args["ownerName"]) &&
			!empty($args["ownerName"]) &&
			$args["ownerName"] != "null"
		) {
			$query->join(
				"tblPatientOwnerInformation AS invoice_owners",
				"invoice_owners.id",
				"=",
				"invoices.owner_id",
			);

			$ownerSearchTerm = $args["ownerName"];
			$query->where(function ($query) use ($ownerSearchTerm) {
				$query
					->where(
						"invoice_owners.first_name",
						"like",
						"%" . $ownerSearchTerm . "%",
					)
					->orWhere(
						"invoice_owners.last_name",
						"like",
						"%" . $ownerSearchTerm . "%",
					);
			});
		}

		if (
			isset($args["patientName"]) &&
			!empty($args["patientName"]) &&
			$args["patientName"] != "null"
		) {
			$query->join(
				"tblClients AS invoice_patients",
				"invoice_patients.id",
				"=",
				"invoices.patient_id",
			);
			$query->where(
				"invoice_patients.first_name",
				"like",
				"%" . $args["patientName"] . "%",
			);
		}

		if (isset($args["ownerId"])) {
			$query->where("owner_id", $args["ownerId"]);
		}

		if (isset($args["patientId"])) {
			$query->where("patient_id", $args["patientId"]);
		}

		if (isset($args["locationId"])) {
			$query->where("location_id", $args["locationId"]);
		}

		if (isset($args["invoiceIds"])) {
			$query->whereIn("id", $args["invoiceIds"]);
		}

		if (isset($args["patientIds"])) {
			$query->whereIn("patient_id", $args["patientIds"]);
		}

		return $query;
	}
}
