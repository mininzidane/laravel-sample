<?php

namespace App\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VaccineHistory extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request));
		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$patientId = $this->getQueryParameters()["patientId"];

		if ($patientId) {
			$patient = DB::connection($this->getConnectionName())->selectOne(
				DB::raw(
					"
					select tblPatientOwnerInformation.first_name as owner_first_name,
						   tblPatientOwnerInformation.last_name as owner_last_name,
						   tblClients.first_name as client_first_name,
						   tblClients.license as client_license,
						   tblGenders.gender as client_gender,
						   tblClients.microchip as client_microchip,
						   tblClients.species as client_species,
						   GROUP_CONCAT(DISTINCT tblPatientAnimalBreeds.breed ORDER BY tblPatientAnimalBreeds.breed SEPARATOR ', ') as client_breed,
						   tblOrganizationLocations.name as location_name,
						   tblOrganizationLocations.image_name as location_logo,
						   tblOrganizations.image_name as organization_logo
					from tblClients
					left join tblPatientOwners on tblPatientOwners.client_id = tblClients.id and tblPatientOwners.primary = 1
					left join tblPatientOwnerInformation on tblPatientOwnerInformation.id = tblPatientOwners.owner_id
					left join tblPatientAnimalColors on tblPatientAnimalColors.client_id = tblClients.id
					left join tblPatientAnimalBreeds on tblPatientAnimalBreeds.client_id = tblClients.id
					left join tblPatientAnimalMarkings on tblPatientAnimalMarkings.client_id = tblClients.id
					left join tblGenders on tblGenders.id = tblClients.gender_id
					left join tblOrganizationLocations on tblOrganizationLocations.organization_id = tblClients.organization_id
					left join tblOrganizations on tblOrganizations.id = tblClients.organization_id
					where tblClients.id = :patientId
					  and tblOrganizationLocations.deleted_at is null
					  and tblOrganizationLocations.id = :locationId
				",
				),
				$this->getQueryParameters(),
			);

			$vaccines = DB::connection($this->getConnectionName())->select(
				DB::raw(
					"
					SELECT
						tblClients.first_name,
						tblClients.id,
						tblPatientVaccines.vaccine_name,
						suppliers.company_name AS vaccine_manufacturer_supplier,
						tblPatientVaccines.receiving_item_lot_number AS vaccine_receiving_item_lot_number,
						tblPatientVaccines.receiving_item_expiration_date AS vaccine_receiving_item_expiration_date,
						tblPatientVaccines.administered_date AS administered_date,
						tblPatientVaccines.serialnumber AS serial_number,
						tblUsers.first_name AS provider_first_name,
						tblUsers.last_name AS provider_last_name,
						tblUsers.degree AS provider_degree,
						tblUsers.sig_name AS provider_sig_name,
						tblUsers.license AS provider_license,
						tblOrganizationLocations.name AS location_name,
						tblOrganizationLocations.address1 AS location_address,
						tblOrganizationLocations.city AS location_city,
						provider_region.name AS location_state,
						tblOrganizationLocations.zip AS location_zip,
						reminders.due_date AS vaccine_next_due
					FROM
						tblPatientVaccines
						LEFT JOIN tblClients ON tblClients.id = tblPatientVaccines.client_id
						LEFT JOIN items ON items.id = tblPatientVaccines.vaccine_item_id
						LEFT JOIN tblUsers ON tblUsers.id = tblPatientVaccines.seen_by
						LEFT JOIN tblOrganizationLocations ON tblOrganizationLocations.id = tblPatientVaccines.location_administered
						LEFT JOIN tblOrganizations ON tblOrganizations.id = tblOrganizationLocations.organization_id
						LEFT JOIN tblSubRegions AS provider_region ON provider_region.id = tblOrganizationLocations.state
						LEFT JOIN suppliers ON suppliers.id = items.manufacturer_id
					LEFT OUTER JOIN (
								SELECT COALESCE(item_replaces.item_id, tblClientReminders.item_id) AS item_id, 
									   client_id, tblClientReminders.due_date
								  FROM tblClientReminders
								LEFT OUTER JOIN item_replaces ON item_replaces.replaces_item_id = tblClientReminders.item_id 
								WHERE tblClientReminders.deleted_at IS NULL
								  AND item_replaces.deleted_at IS NULL
							   ) AS reminders 
							   ON (
								   reminders.item_id = tblPatientVaccines.vaccine_item_id OR 
								   reminders.item_id = tblPatientVaccines.vaccine_item_id
								   )
							   AND reminders.client_id = tblClients.id
					WHERE tblPatientVaccines.removed = 0
					  AND tblClients.id = :patientId
					  and tblOrganizationLocations.id = :locationId
					  AND tblOrganizationLocations.deleted_at IS NULL
					  AND tblPatientVaccines.deleted_at IS NULL
					ORDER BY tblPatientVaccines.administered_date DESC
				",
				),
				$this->getQueryParameters(),
			);

			if ($patient) {
				return response()->json([
					"patient" => $patient,
					"vaccines" => $vaccines,
				]);
			}

			return response()->json(
				"No vaccination records for that patient ID were found.",
				404,
			);
		}
		return response()->json("A patient ID is required.", 400);
	}

	public function buildParams(Request $request): array
	{
		return [
			"patientId" => $request->input("patientId"),
			"locationId" => $request->input("locations")[0],
		];
	}
}
