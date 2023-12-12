<?php

namespace App\Reports;

use App\Models\Location;
use App\Models\Vaccination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VaccineCertificate extends ReportModel
{
	const REMINDERS_MAX = 5;

	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request));
		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$vaccinationId = explode(",", $this->getQueryParameters()["detailId"]);

		if ($vaccinationId) {
			$results = DB::connection($this->getConnectionName())
				->table("tblPatientVaccines")
				->leftJoin(
					"tblPatientOwnerInformation",
					"tblPatientOwnerInformation.id",
					"=",
					"tblPatientVaccines.current_owner_id",
				)
				->leftJoin(
					"tblClients",
					"tblClients.id",
					"=",
					"tblPatientVaccines.client_id",
				)
				->leftJoin(
					"tblPatientAnimalColors",
					"tblPatientAnimalColors.client_id",
					"=",
					"tblClients.id",
				)
				->leftJoin(
					"tblPatientAnimalBreeds",
					"tblPatientAnimalBreeds.client_id",
					"=",
					"tblClients.id",
				)
				->leftJoin(
					"tblPatientAnimalMarkings",
					"tblPatientAnimalMarkings.client_id",
					"=",
					"tblClients.id",
				)
				->leftJoin(
					"tblClientReminders",
					"tblClientReminders.id",
					"=",
					"tblPatientVaccines.reminder_id",
				)
				->leftJoin(
					"items",
					"items.id",
					"=",
					"tblPatientVaccines.vaccine_item_id",
				)
				->leftJoin(
					"tblUsers",
					"tblUsers.id",
					"=",
					"tblPatientVaccines.seen_by",
				)
				->leftJoin(
					"tblOrganizationLocations",
					"tblOrganizationLocations.id",
					"=",
					"tblPatientVaccines.location_administered",
				)
				->leftJoin(
					"tblOrganizations",
					"tblOrganizations.id",
					"=",
					"tblOrganizationLocations.organization_id",
				)
				->leftJoin(
					"suppliers",
					"suppliers.id",
					"=",
					"items.manufacturer_id",
				)
				// Join Owner and Provider subregion tables separately, so we can get the State Names.
				->leftJoin(
					"tblSubRegions as owner_region",
					"owner_region.id",
					"=",
					"tblPatientOwnerInformation.state",
				)
				->leftJoin(
					"tblSubRegions as provider_region",
					"provider_region.id",
					"=",
					"tblOrganizationLocations.state",
				)
				->select([
					"tblPatientVaccines.id as vaccination_id",
					// Owner Information
					"tblPatientOwnerInformation.first_name as owner_first_name",
					"tblPatientOwnerInformation.last_name as owner_last_name",
					"tblPatientOwnerInformation.phone as owner_phone",
					"tblPatientOwnerInformation.email as owner_email",
					"tblPatientOwnerInformation.address1 as owner_address",
					"tblPatientOwnerInformation.city as owner_city",
					"owner_region.name as owner_state",
					"tblPatientOwnerInformation.zip as owner_zip",
					// Animal Information
					"tblClients.id as client_id",
					"tblClients.first_name as client_first_name",
					"tblClients.last_name as client_last_name",
					"tblClients.license as client_license",
					"tblPatientVaccines.current_gender as client_gender",
					"tblClients.microchip as client_microchip",
					"tblClients.species as client_species",
					DB::raw(
						"GROUP_CONCAT(DISTINCT tblPatientAnimalBreeds.breed ORDER BY tblPatientAnimalBreeds.breed SEPARATOR ', ') as client_breed",
					),
					DB::raw(
						"GROUP_CONCAT(DISTINCT tblPatientAnimalMarkings.markings ORDER BY tblPatientAnimalMarkings.markings SEPARATOR ', ') as client_marking",
					),
					DB::raw(
						"GROUP_CONCAT(DISTINCT tblPatientAnimalColors.color ORDER BY tblPatientAnimalColors.color SEPARATOR ', ') as client_color",
					),
					"tblClients.date_of_birth as client_date_of_birth",
					"tblPatientVaccines.current_weight as client_current_weight",
					// Information from vaccine table related to the time of vaccination.
					"tblPatientVaccines.vaccine_name",
					"tblPatientVaccines.receiving_item_lot_number as vaccine_receiving_item_lot_number",
					"tblPatientVaccines.receiving_item_expiration_date as vaccine_receiving_item_expiration_date",
					"tblPatientVaccines.administered_date as vaccine_administered_date",
					// Item Information
					"tblPatientVaccines.serialnumber as serial_number",
					// Provider Information
					"tblUsers.first_name as provider_first_name",
					"tblUsers.last_name as provider_last_name",
					"tblUsers.degree as provider_degree",
					"tblUsers.sig_name as provider_sig_name",
					"tblUsers.license as provider_license_number",
					// Organization Information
					"tblOrganizationLocations.name as location_name",
					"tblOrganizationLocations.address1 as location_address",
					"tblOrganizationLocations.city as location_city",
					"provider_region.name as location_state",
					"tblOrganizationLocations.zip as location_zip",
					"tblOrganizationLocations.image_name as location_logo",
					"tblOrganizations.image_name as organization_logo",
					"tblClientReminders.due_date as vaccine_next_due",
					DB::raw("null AS formatted_age"),
					"suppliers.company_name as vaccine_manufacturer_supplier",
					DB::raw(
						"(SELECT units FROM tblOrganizations LIMIT 1) as units",
					),
				])
				->whereNull("tblPatientVaccines.deleted_at")
				->whereIn("tblPatientVaccines.id", $vaccinationId)
				->groupBy("tblPatientVaccines.id")
				->get()
				->map(function ($result) {
					if (!$result->vaccine_next_due) {
						// Item Kits may contain a Vaccination that has a replacement item reminder.
						$result->vaccine_next_due =
							DB::connection($this->getConnectionName())
								->table("tblClientReminders")
								->leftJoin(
									"item_replaces",
									"item_replaces.replaces_item_id",
									"=",
									"tblClientReminders.item_id",
								)
								->leftJoin("tblPatientVaccines", function (
									$join
								) {
									$join->on(
										"tblPatientVaccines.vaccine_item_id",
										"=",
										"item_replaces.item_id",
									);
									$join->on(
										"tblPatientVaccines.invoice_id",
										"=",
										"tblClientReminders.invoice_id",
									);
								})
								->select([
									"tblClientReminders.due_date",
									"tblClientReminders.description",
								])
								->where(
									"tblPatientVaccines.id",
									"=",
									$result->vaccination_id,
								)
								->where("tblClientReminders.removed", "=", 0)
								->whereNull("tblPatientVaccines.deleted_at")
								->whereNull("tblClientReminders.deleted_at")
								->first()->due_date ?? null;
					}

					if (!$result->formatted_age) {
						$vaccine = Vaccination::on(
							$this->getConnectionName(),
						)->find($result->vaccination_id);

						$result->formatted_age = $vaccine->formattedPatientsAge;
					}

					return $result;
				});

			if (!$results->isEmpty()) {
				$reminders = DB::connection($this->getConnectionName())
					->table("tblClientReminders")
					->select([
						"tblClientReminders.due_date",
						"tblClientReminders.description",
					])
					// TODO: What if multiple clients are retrieved? Will a report ever run for multiple clients?
					->where(
						"tblClientReminders.client_id",
						"=",
						$results[0]->client_id,
					)
					->where("tblClientReminders.removed", "=", 0)
					->whereNull("tblClientReminders.deleted_at")
					->whereRaw("tblClientReminders.due_date > NOW()")
					->limit(self::REMINDERS_MAX)
					->orderBy("due_date")
					->get();

				$location = Location::on($this->getConnectionName())
					->where("id", request()["locations"][0])
					->with("subregion", "organization", "tz")
					->first();

				return response()->json([
					"results" => $results,
					"reminders" => $reminders ?? [],
					"location" => $location,
				]);
			}

			return response()->json(
				"No vaccination record with that ID was found.",
				404,
			);
		}

		return response()->json("A vaccination ID is required.", 400);
	}

	public function buildParams(Request $request): array
	{
		return [
			"detailId" => $request->input("detailId"),
		];
	}
}
