<?php

namespace App\Reports;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RemindersList extends ReportModel
{
	protected $logo;

	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request))
			->setLocationPredicate($request)
			->setReplicaConnection($request);

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$location = Location::on($this->getConnectionName())->find(
			request()["locations"][0],
		);
		$logo =
			count(request()["locations"]) > 1
				? $location->organization->imageUrl
				: $location->imageUrl ?? $location->organization->imageUrl;
		if (!$logo) {
			$logo = "https://hippo-app.s3.amazonaws.com/img/logo.png";
		}

		$remindersList = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"
                SELECT
                     owner_id,
                     loc_id,
                     client_id,
                     substring_index(group_concat(remind_list SEPARATOR '<br> '), '<br> ', 4) remind_list,
                     patient_name,
                     owner_first,
                     owner_mid,
                     owner_last,
                     owner_add1,
                     owner_add2,
                     owner_phone,
                     owner_email,
                     owner_city,
                     owner_zip,
                     owner_state,
                     location_name,
                     sum(reminder_count) reminder_count
                FROM (
                     -- Reminders
                     SELECT
                         tblPatientOwners.owner_id AS owner_id,
                         tbl_reminder_location.id AS loc_id,
                         1 AS sort_order,
                         tblClientReminders.client_id AS client_id,
                         substring_index(
                            group_concat(
                               CONCAT(tblClientReminders.description,
                                      ' on ',
                                      date_format(tblClientReminders.due_date, '%m-%d-%Y')
                                     ) SEPARATOR '<br> '
                               ), '<br> ', 4
                            ) AS remind_list,
                         tblClients.first_name AS patient_name,
                         tblPatientOwnerInformation.first_name AS owner_first,
                         tblPatientOwnerInformation.middle_name AS owner_mid,
                         tblPatientOwnerInformation.last_name AS owner_last,
                         tblPatientOwnerInformation.address1 AS owner_add1,
                         tblPatientOwnerInformation.address2 AS owner_add2,
                         tblPatientOwnerInformation.city AS owner_city,
                         tblPatientOwnerInformation.zip AS owner_zip,
                         tbl_client_state.name AS owner_state,
                         tblPatientOwnerInformation.phone AS owner_phone,
                         tblPatientOwnerInformation.email AS owner_email,
                         tbl_reminder_location.name AS location_name,
                         counts.count AS reminder_count
                     FROM
                         tblClientReminders
                         LEFT JOIN tblClients ON tblClientReminders.client_id = tblClients.id
                         LEFT JOIN tblPatientOwners ON tblClientReminders.client_id = tblPatientOwners.client_id AND tblPatientOwners.primary = 1
                         LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
                         LEFT JOIN tblSubRegions AS tbl_client_state ON tblPatientOwnerInformation.state = tbl_client_state.id
                         LEFT JOIN invoices ON tblClientReminders.sale_id = invoices.id AND invoices.status_id in(1, 2)
                         LEFT JOIN tblOrganizationLocations AS tbl_reminder_location ON tblClientReminders.location_id = tbl_reminder_location.id
                         LEFT JOIN tblSubRegions AS tbl_sale_loc_state ON tbl_reminder_location.state = tbl_sale_loc_state.id
                         JOIN (
                             SELECT
                                 COUNT(*) AS count,
                                 MAX(tblClients.id) AS client_id
                             FROM
                                 tblClientReminders
                                 LEFT JOIN tblClients ON tblClientReminders.client_id = tblClients.id
                                 LEFT JOIN tblPatientOwners ON tblClientReminders.client_id = tblPatientOwners.client_id AND tblPatientOwners.primary = 1
                                 LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
                                 LEFT JOIN tblSubRegions AS tbl_client_state ON tblPatientOwnerInformation.state = tbl_client_state.id
                                 LEFT JOIN invoices ON tblClientReminders.sale_id = invoices.id AND invoices.status_id in(1, 2)
                                 LEFT JOIN tblOrganizationLocations AS tbl_reminder_location ON tblClientReminders.location_id = tbl_reminder_location.id
                                 LEFT JOIN tblSubRegions AS tbl_sale_loc_state ON tbl_reminder_location.state = tbl_sale_loc_state.id
                             WHERE
                                 tblClientReminders.removed = 0
                                 AND (tblClients.date_of_death IS NULL OR tblClients.date_of_death = '0000-00-00')
                                 AND tblClientReminders.due_date BETWEEN :beginDate1 AND :endDate1 
                                 AND " .
					$this->getLocationPredicate() .
					"
					             AND LENGTH(tblClientReminders.description) > 0
                             GROUP BY
                                 tblClients.id,
                                 tblPatientOwners.owner_id) counts ON tblClients.id = counts.client_id
                         WHERE tblClientReminders.removed = 0
                         AND (tblClients.date_of_death IS NULL OR tblClients.date_of_death = '0000-00-00')
                         AND " .
					$this->getLocationPredicate() .
					"
                         AND tblClientReminders.due_date BETWEEN :beginDate2 AND :endDate2       
                         GROUP BY
                             tblClients.id,
                             tblPatientOwners.owner_id
                         UNION ALL
                         -- Appointments
                         SELECT
                             tblPatientOwners.owner_id AS owner_id,
                             tbl_reminder_location.id AS loc_id,
                             2 AS sort_order,
                             tblSchedule.client_id AS client_id,
                             substring_index(
                                group_concat(
                                   CONCAT(tblSchedulerEvents.name,
                                          ' on ',
                                          date_format(tblSchedule.start_time, '%m-%d-%Y')
                                          ) SEPARATOR '<br> '
                                   ), '<br> ', 4
                                ) AS remind_list,
                             tblClients.first_name AS patient_name,
                             tblPatientOwnerInformation.first_name AS owner_first,
                             tblPatientOwnerInformation.middle_name AS owner_mid,
                             tblPatientOwnerInformation.last_name AS owner_last,
                             tblPatientOwnerInformation.address1 AS owner_add1,
                             tblPatientOwnerInformation.address2 AS owner_add2,
                             tblPatientOwnerInformation.city AS owner_city,
                             tblPatientOwnerInformation.zip AS owner_zip,
                             tbl_client_state.name AS owner_state,
                             tblPatientOwnerInformation.phone AS owner_phone,
                             tblPatientOwnerInformation.email AS owner_email,
                             tbl_reminder_location.name AS location_name,
                             counts.count AS reminder_count
                         FROM
                             tblSchedule
                             INNER JOIN tblSchedulerEvents ON tblSchedule.event_id = tblSchedulerEvents.id
                             LEFT JOIN tblClients ON tblSchedule.client_id = tblClients.id
                             LEFT JOIN tblPatientOwners ON tblSchedule.client_id = tblPatientOwners.client_id
                                 AND tblPatientOwners.primary = 1
                         LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
                         LEFT JOIN tblSubRegions AS tbl_client_state ON tblPatientOwnerInformation.state = tbl_client_state.id
                         LEFT JOIN tblSchedulerResources ON tblSchedule.resource_id = tblSchedulerResources.id
                         LEFT JOIN tblOrganizationLocations AS tbl_reminder_location ON tblSchedulerResources.location_id = tbl_reminder_location.id                         
                         LEFT JOIN tblSubRegions AS tbl_sale_loc_state ON tbl_reminder_location.state = tbl_sale_loc_state.id
                         JOIN (
                             SELECT
                                 COUNT(*) AS count,
                                 MAX(tblClients.id) AS client_id
                             FROM  tblSchedule
                             INNER JOIN tblSchedulerEvents ON tblSchedule.event_id = tblSchedulerEvents.id
                             LEFT JOIN tblClients ON tblSchedule.client_id = tblClients.id
                             LEFT JOIN tblPatientOwners ON tblSchedule.client_id = tblPatientOwners.client_id
                                 AND tblPatientOwners.primary = 1
                             LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
                             LEFT JOIN tblSubRegions AS tbl_client_state ON tblPatientOwnerInformation.state = tbl_client_state.id
                             LEFT JOIN tblSchedulerResources ON tblSchedule.resource_id = tblSchedulerResources.id
                             LEFT JOIN tblOrganizationLocations AS tbl_reminder_location ON tblSchedulerResources.location_id = tbl_reminder_location.id
                             LEFT JOIN tblSubRegions AS tbl_sale_loc_state ON tbl_reminder_location.state = tbl_sale_loc_state.id
                         WHERE
                             tblSchedule.removed = 0
                             AND tblSchedule.status = 'pending'
                             AND (tblClients.date_of_death IS NULL OR tblClients.date_of_death = '0000-00-00')
                             AND tblSchedule.blocked = 0
                             AND tblSchedule.start_time BETWEEN :beginDate3 AND :endDate3
                             AND FIND_IN_SET(tbl_reminder_location.id, :locations1)
                             AND LENGTH(tblSchedulerEvents.name) > 0
                         GROUP BY
                             tblClients.id) counts ON tblClients.id = counts.client_id
                     WHERE
                         tblSchedule.removed = 0
                         AND tblSchedule.status = 'pending'
                         AND (tblClients.date_of_death IS NULL OR tblClients.date_of_death = '0000-00-00')
                         AND tblSchedule.blocked = 0
                         AND tblSchedule.start_time BETWEEN :beginDate4 AND :endDate4
                         AND FIND_IN_SET(tbl_reminder_location.id, :locations2)
                         AND LENGTH(tblSchedulerEvents.name) > 0
                     GROUP BY
                         tblClients.id,
                         tblPatientOwners.owner_id
                     ORDER BY
                         sort_order ASC,
                         client_id) T
                 WHERE
                     -- sort_order is a misnomer - this is a tag for including reminders and appts or both 
                     CASE :include
                     WHEN 1 THEN
                         sort_order = 1
                     WHEN 2 THEN
                         sort_order = 2
                     ELSE
                         owner_id IS NOT NULL
                     END
                 GROUP BY
                     client_id
                 ORDER BY
                 	owner_last,
                 	owner_first
				",
			),
			$this->getQueryParameters(),
		);

		return response()->json([
			"reminders" => $remindersList,
			"logo" => $logo,
		]);
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations1" => implode(",", $request->input("locations")),
			"locations2" => implode(",", $request->input("locations")),
			"beginDate1" => $request->input("beginDate"),
			"endDate1" => $request->input("endDate"),
			"beginDate2" => $request->input("beginDate"),
			"endDate2" => $request->input("endDate"),
			"beginDate3" => $request->input("beginDate"),
			"endDate3" => $request->input("endDate"),
			"beginDate4" => $request->input("beginDate"),
			"endDate4" => $request->input("endDate"),
			"include" => $request->input("includeReminders"),
		];
	}
}
