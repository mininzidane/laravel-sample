<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnniversaryList extends ReportModel
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
		// client / patient relationship not needed, remove later
		$results = DB::connection($this->getConnectionName())->select(
			DB::raw("
                select 
                    CONCAT (locations.name, ' (', sales.location_id, ')') as location_name,
                    DATE_FORMAT(CONVERT_TZ(sales.first_sale, 'UTC', tblTimezones.php_supported), '%m-%d-%Y') as first_visit,
                    sales.owner_id,
                    sales.location_id,
                    sales.patient_id,
                    tblClients.first_name as patient,
                    CONCAT(tblPatientOwnerInformation.first_name, ' ', tblPatientOwnerInformation.last_name) as owner_name,
                    tblPatientOwnerInformation.address1,
                    tblPatientOwnerInformation.address2,
                    tblPatientOwnerInformation.city,
                    tblPatientOwnerInformation.state,
                    tblPatientOwnerInformation.zip,
                    tblPatientOwnerInformation.phone,
                    tblPatientOwnerInformation.email,
                    TIMESTAMPDIFF(YEAR,
                                  DATE_FORMAT(CONVERT_TZ(sales.first_sale, 'UTC', tblTimezones.php_supported), '%Y-%m-%d'),   
                                  CURDATE()
                                 ) as years
                from (select min(invoices.completed_at) as first_sale, 
                             invoices.patient_id,
                             invoices.owner_id,
                             invoices.location_id
                        from invoices
                       where invoices.status_id = 2
                         and TIMESTAMPDIFF(YEAR,
                                           invoices.completed_at,   
                                           CURDATE()
                                          ) > 0
                         and FIND_IN_SET(location_id, :locations)
                         and invoices.completed_at IS NOT NULL
                       group by owner_id, invoices.location_id) as sales
                inner join tblClients on sales.patient_id = tblClients.id
                                     and tblClients.deleted_at IS NULL
                                     and (
                                          tblClients.date_of_death = '0000-00-00'
                                        or tblClients.date_of_death IS NULL)
                inner join tblPatientOwners on tblClients.id = tblPatientOwners.client_id 
                                           and tblPatientOwners.primary = 1 
                                           and tblPatientOwners.deleted_at IS NULL
                inner join tblPatientOwnerInformation on tblPatientOwners.owner_id = tblPatientOwnerInformation.id
                inner join tblOrganizationLocations AS locations ON sales.location_id = locations.id
                left join tblTimezones ON locations.timezone = tblTimezones.id
                where DATE_FORMAT(CONVERT_TZ(sales.first_sale, 'UTC', tblTimezones.php_supported),'%m-%d') 
                       BETWEEN DATE_FORMAT(:beginDate,'%m-%d') 
                           AND DATE_FORMAT(:endDate,'%m-%d') 
                order by owner_name, first_sale, patient;
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
