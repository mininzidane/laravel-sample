<?php

namespace App\GraphQL\Resolvers;

use Carbon\Carbon;
use Closure;

class PatientResolver extends HippoResolver
{
	public function getQuery(Closure $getSelectFields)
	{
		$query = $this->resolver->getQuery($getSelectFields);

		$args = $this->resolver->getArgs();

		//$sort = $args['sort'] ?? '';

		$query->distinct();

		if (isset($args["ownerId"]) && $args["ownerId"]) {
			return $query
				->leftJoin(
					"tblPatientOwners",
					"tblClients.id",
					"=",
					"tblPatientOwners.client_id",
				)
				->where("owner_id", $args["ownerId"])
				->whereNull("tblPatientOwners.deleted_at");
		}

		if (isset($args["patientId"]) && $args["patientId"]) {
			$query->where("patient_id", $args["patientId"]);
		}

		$query
			->leftJoin(
				"tblPatientOwners",
				"tblClients.id",
				"=",
				"tblPatientOwners.client_id",
			)
			->leftJoin(
				"tblPatientOwnerInformation",
				"tblPatientOwners.owner_id",
				"=",
				"tblPatientOwnerInformation.id",
			);

		if (isset($args["patientName"]) && $args["patientName"]) {
			if (str_contains($args["patientName"], " ")) {
				$array = explode(" ", $args["patientName"], 2);
				$firstName = $array[0] ?? "";
				$lastName = $array[1] ?? "";

				$query->where(function ($query) use ($firstName, $lastName) {
					$query
						->where(
							"tblClients.first_name",
							"like",
							"%" . $firstName . "%",
						)
						->where(
							"tblPatientOwnerInformation.last_name",
							"like",
							"%" . $lastName . "%",
						);
				});
			} else {
				$query->where(
					"tblClients.first_name",
					"like",
					"%" . $args["patientName"] . "%",
				);
			}
		}

		if (isset($args["licenseId"]) && $args["licenseId"]) {
			$query->where("license", "like", "%" . $args["licenseId"] . "%");
		}

		if (isset($args["microchipId"]) && $args["microchipId"]) {
			$query->where(
				"microchip",
				"like",
				"%" . $args["microchipId"] . "%",
			);
		}

		if (isset($args["breed"]) && $args["breed"]) {
			$query->where("breed", "=", $args["breed"]);
		}

		if (isset($args["species"]) && $args["species"]) {
			$query->where("species", "=", $args["species"]);
		}

		if (
			(isset($args["ownerName"]) && $args["ownerName"]) ||
			(isset($args["email"]) && $args["email"]) ||
			(isset($args["phone"]) && $args["phone"])
		) {
			if (isset($args["ownerName"]) && $args["ownerName"]) {
				$ownerName = $args["ownerName"];
				if (str_contains($args["ownerName"], " ")) {
					$array = explode(" ", $args["ownerName"], 2);
					$firstName = $array[0] ?? "";
					$lastName = $array[1] ?? "";

					$query->where(function ($query) use (
						$firstName,
						$lastName
					) {
						$query
							->where(
								"tblPatientOwnerInformation.first_name",
								"like",
								"%" . $firstName . "%",
							)
							->where(
								"tblPatientOwnerInformation.last_name",
								"like",
								"%" . $lastName . "%",
							);
					});
				}
			} else {
				$query->where(function ($query) use ($ownerName) {
					$query
						->where(
							"tblPatientOwnerInformation.first_name",
							"like",
							"%" . $ownerName . "%",
						)
						->orWhere(
							"tblPatientOwnerInformation.last_name",
							"like",
							"%" . $ownerName . "%",
						);
				});
			}
		}

		if (isset($args["email"]) && $args["email"]) {
			$email = $args["email"];
			$query->where(function ($query) use ($email) {
				$query->where(
					"tblPatientOwnerInformation.email",
					"like",
					"%" . $email . "%",
				);
			});
		}

		if (isset($args["phone"]) && $args["phone"]) {
			$phone = $args["phone"];
			$query->where(function ($query) use ($phone) {
				$query
					->where(
						"tblPatientOwnerInformation.phone",
						"like",
						"%" . $phone . "%",
					)
					->orWhere(
						"tblPatientOwnerInformation.phone_2",
						"like",
						"%" . $phone . "%",
					)
					->orWhere(
						"tblPatientOwnerInformation.phone_3",
						"like",
						"%" . $phone . "%",
					);
			});
		}

		if (isset($args["serialNumber"]) && $args["serialNumber"]) {
			$serialNumber = $args["serialNumber"];
			$query
				->leftJoin(
					"invoices",
					"tblClients.id",
					"=",
					"invoices.patient_id",
				)
				->leftJoin(
					"invoice_items",
					"invoices.id",
					"=",
					"invoice_items.invoice_id",
				)
				->leftJoin(
					"tblPatientVaccines",
					"tblClients.id",
					"=",
					"tblPatientVaccines.client_id",
				)
				->where(function ($query) use ($serialNumber) {
					$query
						->where(
							"invoice_items.serial_number",
							"like",
							"%" . $serialNumber . "%",
						)
						->orWhere(
							"tblPatientVaccines.serialnumber",
							"like",
							"%" . $serialNumber . "%",
						);
				});
		}

		if (!isset($args["deceased"]) || !$args["deceased"]) {
			$query->where(function ($query) {
				$query
					->whereNull("date_of_death")
					->orWhere("date_of_death", "=", "0000-00-00");
			});
		}

		if (isset($args["smartSearch"]) && $args["smartSearch"]) {
			$smartSearch = $args["smartSearch"];

			$query->where(function ($query) use ($smartSearch) {
				$query->where(
					"tblClients.id",
					"like",
					"%" . $smartSearch . "%",
				);

				$query->orWhere(
					"tblClients.alias_id",
					"like",
					"%" . $smartSearch . "%",
				);

				$query->orWhere(
					"tblClients.first_name",
					"like",
					"%" . $smartSearch . "%",
				);

				$query->orWhere("license", "like", "%" . $smartSearch . "%");

				$query->orWhere("microchip", "like", "%" . $smartSearch . "%");

				if (str_contains($smartSearch, " ")) {
					$array = explode(" ", $smartSearch, 2);
					$firstName = $array[0] ?? "";
					$lastName = $array[1] ?? "";

					$query->orWhere(function ($query) use (
						$firstName,
						$lastName
					) {
						$query
							->where(
								"tblPatientOwnerInformation.first_name",
								"like",
								"%" . $firstName . "%",
							)
							->where(
								"tblPatientOwnerInformation.last_name",
								"like",
								"%" . $lastName . "%",
							);
					});

					$query->orWhere(function ($query) use (
						$firstName,
						$lastName
					) {
						$query
							->where(
								"tblClients.first_name",
								"like",
								"%" . $firstName . "%",
							)
							->where(
								"tblPatientOwnerInformation.last_name",
								"like",
								"%" . $lastName . "%",
							);
					});
				} else {
					$query->orWhere(function ($query) use ($smartSearch) {
						$query
							->orWhere(
								"tblPatientOwnerInformation.first_name",
								"like",
								"%" . $smartSearch . "%",
							)
							->orWhere(
								"tblPatientOwnerInformation.last_name",
								"like",
								"%" . $smartSearch . "%",
							)
							->orWhere(
								"tblPatientOwnerInformation.id",
								"like",
								"%" . $smartSearch . "%",
							);
					});
				}

				$query->orWhere(function ($query) use ($smartSearch) {
					$query
						->orWhere(
							"tblPatientOwnerInformation.email",
							"like",
							"%" . $smartSearch . "%",
						)
						->orWhere(
							"tblPatientOwnerInformation.phone",
							"like",
							"%" . $smartSearch . "%",
						)
						->orWhere(
							"tblPatientOwnerInformation.phone_2",
							"like",
							"%" . $smartSearch . "%",
						)
						->orWhere(
							"tblPatientOwnerInformation.phone_3",
							"like",
							"%" . $smartSearch . "%",
						);
				});
			});
		}

		return $query;
	}
}
