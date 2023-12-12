<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CondolencesList extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters(
			$this->buildParams($request),
		)->setReplicaConnection($request);

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$results = DB::connection($this->getConnectionName())->select(
			DB::raw("
                SELECT Distinct(tblClients.id), 
                    tblClients.first_name as patient_name,
                    DATE_FORMAT(CONVERT_TZ(tblClients.date_of_death, 'UTC', tblTimezones.php_supported), '%M %d, %Y') as date_of_death,   
                    tblGenders.gender, 
                    tblClients.species, 
                    tblClients.first_name AS patient_name,
                    tblPatientOwnerInformation.id AS owner_id,
                    concat(tblPatientOwnerInformation.last_name, ', ', tblPatientOwnerInformation.first_name) AS owner_name,
                    tblPatientOwnerInformation.phone AS owner_phone,
                    tblPatientOwnerInformation.email AS owner_email
                FROM tblClients
                INNER JOIN tblPatientOwners on tblClients.id = tblPatientOwners.client_id
                LEFT JOIN tblGenders on tblClients.gender_id = tblGenders.id    
                INNER JOIN tblPatientOwnerInformation on tblPatientOwners.owner_id = tblPatientOwnerInformation.id
                INNER JOIN tblOrganizationLocations on tblClients.organization_id = tblOrganizationLocations.organization_id  
                LEFT JOIN tblTimezones ON tblOrganizationLocations.timezone = tblTimezones.id
                WHERE FIND_IN_SET(tblOrganizationLocations.id, :locations) 
                    AND tblPatientOwners.primary = 1
                    AND tblClients.date_of_death 
                       BETWEEN CONVERT_TZ(:beginDate, 'UTC',  tblTimezones.php_supported) 
                           AND CONVERT_TZ(:endDate, 'UTC',  tblTimezones.php_supported)                         
                    ORDER BY owner_name, tblClients.id DESC                
	        "),
			$this->getQueryParameters(),
		);

		return response()->json($results);
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
		];
	}
}
