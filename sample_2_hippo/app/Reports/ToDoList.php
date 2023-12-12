<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Location;

class ToDoList extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request));
		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$toDoListItems = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"
			SELECT
				'Email Note' AS title,
				'unsigned' AS status,
				tblChartEmail.id,
				tblChartEmail.seen_by AS user_id,
				tblChartEmail.organization_id,
				tblChartEmail.client_id,
				tblChartEmail.note AS description,
				tblChartEmail.date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.id AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
			tblChartEmail
				LEFT JOIN tblClients ON tblChartEmail.client_id = tblClients.id
				LEFT JOIN tblPatientOwners ON tblChartEmail.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
				LEFT JOIN tblUsers ON tblChartEmail.seen_by = tblUsers.id
			WHERE
				tblChartEmail.signed = 0
				AND tblChartEmail.removed = 0
				AND tblChartEmail.location_id = :location1
				AND tblPatientOwners.primary = 1
				AND tblChartEmail.deleted_at IS NULL
				AND DATE(CONVERT_TZ(tblChartEmail.date, 'UTC', :timeZone1)) BETWEEN :beginDate1 AND :endDate1

				UNION ALL

			SELECT
				'History Note' AS title,
				'unsigned' AS status,
				tblChartHistory.id,
				tblChartHistory.seen_by AS user_id,
				tblChartHistory.organization_id,
				tblChartHistory.client_id,
				tblChartHistory.note AS description,
				tblChartHistory.date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.id AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
			tblChartHistory
				LEFT JOIN tblClients ON tblChartHistory.client_id = tblClients.id
				LEFT JOIN tblPatientOwners ON tblChartHistory.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
				LEFT JOIN tblUsers ON tblChartHistory.seen_by = tblUsers.id
			WHERE
				tblChartHistory.signed = 0
				AND tblChartHistory.removed = 0
				AND tblChartHistory.location_id = :location2
				AND tblPatientOwners.primary = 1
				AND tblChartHistory.deleted_at IS NULL
				AND DATE(CONVERT_TZ(tblChartHistory.date, 'UTC', :timeZone2)) BETWEEN :beginDate2 AND :endDate2

			UNION ALL

			SELECT
				'Phone Note' AS title,
				'unsigned' AS status,
				tblChartPhone.id,
				tblChartPhone.seen_by AS user_id,
				tblChartPhone.organization_id,
				tblChartPhone.client_id,
				tblChartPhone.note AS description,
				tblChartPhone.date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.id AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblChartPhone
				LEFT JOIN tblClients ON tblChartPhone.client_id = tblClients.id
				LEFT JOIN tblPatientOwners ON tblChartPhone.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
				LEFT JOIN tblUsers ON tblChartPhone.seen_by = tblUsers.id
			WHERE
				tblChartPhone.signed = 0
				AND tblChartPhone.removed = 0
				AND tblChartPhone.location_id = :location3
				AND tblPatientOwners.primary = 1
				AND tblChartPhone.deleted_at IS NULL
				AND DATE(CONVERT_TZ(tblChartPhone.date, 'UTC', :timeZone3)) BETWEEN :beginDate3 AND :endDate3

			UNION ALL

			SELECT
				'Progress Note' AS title,
				'unsigned' AS status,
				tblChartProgress.id,
				tblChartProgress.seen_by AS user_id,
				tblChartProgress.organization_id,
				tblChartProgress.client_id,
				tblChartProgress.note AS description,
				tblChartProgress.date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.id AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
			tblChartProgress
				LEFT JOIN tblClients ON tblChartProgress.client_id = tblClients.id
				LEFT JOIN tblPatientOwners ON tblChartProgress.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
				LEFT JOIN tblUsers ON tblChartProgress.seen_by = tblUsers.id
			WHERE
				tblChartProgress.signed = 0
				AND tblChartProgress.removed = 0
				AND tblChartProgress.location_id = :location4
				AND tblPatientOwners.primary = 1
				AND tblChartProgress.deleted_at IS NULL
				AND DATE(CONVERT_TZ(tblChartProgress.date, 'UTC', :timeZone4)) BETWEEN :beginDate4 AND :endDate4

			UNION ALL
				
			SELECT
				'Treatment Note' AS title,
				'unsigned' AS status,
				tblChartTreatment.id,
				tblChartTreatment.seen_by AS user_id,
				tblChartTreatment.organization_id,
				tblChartTreatment.client_id,
				tblChartTreatment.note AS description,
				tblChartTreatment.date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.id AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
			tblChartTreatment
				LEFT JOIN tblClients ON tblChartTreatment.client_id = tblClients.id
				LEFT JOIN tblPatientOwners ON tblChartTreatment.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
				LEFT JOIN tblUsers ON tblChartTreatment.seen_by = tblUsers.id
			WHERE
				tblChartTreatment.signed = 0
				AND tblChartTreatment.removed = 0
				AND tblChartTreatment.location_id = :location5
				AND tblPatientOwners.primary = 1
				AND tblChartTreatment.deleted_at IS NULL
				AND DATE(CONVERT_TZ(tblChartTreatment.date, 'UTC', :timeZone5)) BETWEEN :beginDate5 AND :endDate5

			UNION ALL

			SELECT
				'SOAP Note' AS title,
				'unsigned' AS status,
				tblChartSoap.id,
				tblChartSoap.seen_by AS user_id,
				tblChartSoap.organization_id,
				tblChartSoap.client_id,
				CONCAT(tblChartSoap.soap_s, ' ' , tblChartSoap.soap_o, ' ', tblChartSoap.soap_a, ' ', tblChartSoap.soap_p) AS description,
				tblChartSoap.date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblChartSoap
				LEFT JOIN tblClients ON tblChartSoap.client_id = tblClients.id
				LEFT JOIN tblPatientOwners ON tblChartSoap.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.ID
				LEFT JOIN tblUsers ON tblChartSoap.seen_by = tblUsers.id
			WHERE
				tblChartSoap.signed = 0
				AND tblChartSoap.removed = 0
				AND tblChartSoap.location_id = :location6
				AND tblPatientOwners.primary = 1
				AND tblChartSoap.deleted_at IS NULL
				AND DATE(CONVERT_TZ(tblChartSoap.date, 'UTC', :timeZone6)) BETWEEN :beginDate6 AND :endDate6

			UNION ALL

			SELECT
				'Medication' AS title,
				'unsigned' AS status,
				tblMedicationPrescriptions.id,
				tblChartEmail.seen_by AS user_id,
				tblMedicationPrescriptions.organization_id,
				tblMedicationPrescriptions.client_id,
				CONCAT(ifnull(items.name, ''), ' - ', ifnull(tblMedicationDispensations.note, ''), ' ', 'QTY:', ifnull(tblMedicationDispensations.qty, ''), ' Units:', ifnull(tblMedicationDispensations.units, '')) AS 'description',
				tblMedicationDispensations.issue_date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblMedicationPrescriptions
				INNER JOIN tblMedicationDispensations ON tblMedicationPrescriptions.id = tblMedicationDispensations.prescription_id
				LEFT JOIN tblClients ON tblMedicationPrescriptions.client_id = tblClients.id
				LEFT JOIN tblChartEmail ON SUBSTR(chart_note, LOCATE('|', chart_note) + 1) = tblChartEmail.id
				LEFT JOIN tblPatientOwners ON tblMedicationPrescriptions.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.ID
				LEFT JOIN tblUsers ON tblChartEmail.seen_by = tblUsers.id
				LEFT JOIN items ON tblMedicationPrescriptions.item_id = items.id
			WHERE
				tblMedicationDispensations.signed = 0
				AND tblMedicationPrescriptions.chart_note LIKE '%Email%'
				AND tblMedicationPrescriptions.removed = 0
				AND tblMedicationDispensations.removed = 0
				AND tblMedicationPrescriptions.location_id = :location7
				AND tblPatientOwners.primary = 1
				AND tblMedicationPrescriptions.deleted_at IS NULL
				AND tblMedicationDispensations.issue_date BETWEEN :beginDate7 AND :endDate7

			UNION ALL

			SELECT
				'Medication' AS title,
				'unsigned' AS status,
				tblMedicationPrescriptions.id,
				tblChartHistory.seen_by AS user_id,
				tblMedicationPrescriptions.organization_id,
				tblMedicationPrescriptions.client_id,
				CONCAT(ifnull(items.name, ''), ' - ', ifnull(tblMedicationDispensations.note, ''), ' ', 'QTY:', ifnull(tblMedicationDispensations.qty, ''), ' Units:', ifnull(tblMedicationDispensations.units, '')) AS 'description',
				tblMedicationDispensations.issue_date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblMedicationPrescriptions
				INNER JOIN tblMedicationDispensations ON tblMedicationPrescriptions.id = tblMedicationDispensations.prescription_id
				LEFT JOIN tblClients ON tblMedicationPrescriptions.client_id = tblClients.id
				LEFT JOIN tblChartHistory ON SUBSTR(chart_note, LOCATE('|', chart_note) + 1) = tblChartHistory.id
				LEFT JOIN tblPatientOwners ON tblMedicationPrescriptions.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.ID
				LEFT JOIN tblUsers ON tblChartHistory.seen_by = tblUsers.id
				LEFT JOIN items ON tblMedicationPrescriptions.item_id = items.id
			WHERE
				tblMedicationDispensations.signed = 0
				AND tblMedicationPrescriptions.chart_note LIKE '%History%'
				AND tblMedicationPrescriptions.removed = 0
				AND tblMedicationDispensations.removed = 0
				AND tblMedicationPrescriptions.location_id = :location8
				AND tblPatientOwners.primary = 1
				AND tblMedicationPrescriptions.deleted_at IS NULL
				AND tblMedicationDispensations.issue_date BETWEEN :beginDate8 AND :endDate8

			UNION ALL

			SELECT
				'Medication' AS title,
				'unsigned' AS status,
				tblMedicationPrescriptions.id,
				tblChartPhone.seen_by AS user_id,
				tblMedicationPrescriptions.organization_id,
				tblMedicationPrescriptions.client_id,
				CONCAT(ifnull(items.name, ''), ' - ', ifnull(tblMedicationDispensations.note, ''), ' ', 'QTY:', ifnull(tblMedicationDispensations.qty, ''), ' Units:', ifnull(tblMedicationDispensations.units, '')) AS 'description',
				tblMedicationDispensations.issue_date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblMedicationPrescriptions
				INNER JOIN tblMedicationDispensations ON tblMedicationPrescriptions.id = tblMedicationDispensations.prescription_id
				LEFT JOIN tblClients ON tblMedicationPrescriptions.client_id = tblClients.id
				LEFT JOIN tblChartPhone ON SUBSTR(chart_note, LOCATE('|', chart_note) + 1) = tblChartPhone.id
				LEFT JOIN tblPatientOwners ON tblMedicationPrescriptions.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.ID
				LEFT JOIN tblUsers ON tblChartPhone.seen_by = tblUsers.id
				LEFT JOIN items ON tblMedicationPrescriptions.item_id = items.id
			WHERE
				tblMedicationDispensations.signed = 0
				AND tblMedicationPrescriptions.chart_note LIKE '%Phone%'
				AND tblMedicationPrescriptions.removed = 0
				AND tblMedicationDispensations.removed = 0
				AND tblMedicationPrescriptions.location_id = :location9
				AND tblPatientOwners.primary = 1
				AND tblMedicationPrescriptions.deleted_at IS NULL
				AND tblMedicationDispensations.issue_date BETWEEN :beginDate9 AND :endDate9

			UNION ALL

			SELECT
				'Medication' AS title,
				'unsigned' AS status,
				tblMedicationPrescriptions.id,
				tblChartProgress.seen_by AS user_id,
				tblMedicationPrescriptions.organization_id,
				tblMedicationPrescriptions.client_id,
				CONCAT(ifnull(items.name, ''), ' - ', ifnull(tblMedicationDispensations.note, ''), ' ', 'QTY:', ifnull(tblMedicationDispensations.qty, ''), ' Units:', ifnull(tblMedicationDispensations.units, '')) AS 'description',
				tblMedicationDispensations.issue_date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblMedicationPrescriptions
				INNER JOIN tblMedicationDispensations ON tblMedicationPrescriptions.id = tblMedicationDispensations.prescription_id
				LEFT JOIN tblClients ON tblMedicationPrescriptions.client_id = tblClients.id
				LEFT JOIN tblChartProgress ON SUBSTR(chart_note, LOCATE('|', chart_note) + 1) = tblChartProgress.id
				LEFT JOIN tblPatientOwners ON tblMedicationPrescriptions.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.ID
				LEFT JOIN tblUsers ON tblChartProgress.seen_by = tblUsers.id
				LEFT JOIN items ON tblMedicationPrescriptions.item_id = items.id
			WHERE
				tblMedicationDispensations.signed = 0
				AND tblMedicationPrescriptions.chart_note LIKE '%Progress%'
				AND tblMedicationPrescriptions.removed = 0
				AND tblMedicationDispensations.removed = 0
				AND tblMedicationPrescriptions.location_id = :location10
				AND tblPatientOwners.primary = 1
				AND tblMedicationPrescriptions.deleted_at IS NULL
				AND tblMedicationDispensations.issue_date BETWEEN :beginDate10 AND :endDate10

			UNION ALL

			SELECT
				'Medication' AS title,
				'unsigned' AS status,
				tblMedicationPrescriptions.id,
				tblChartSoap.seen_by AS user_id,
				tblMedicationPrescriptions.organization_id,
				tblMedicationPrescriptions.client_id,
				CONCAT(ifnull(items.name, ''), ' - ', ifnull(tblMedicationDispensations.note, ''), ' ', 'QTY:', ifnull(tblMedicationDispensations.qty, ''), ' Units:', ifnull(tblMedicationDispensations.units, '')) AS 'description',
				tblMedicationDispensations.issue_date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblMedicationPrescriptions
				INNER JOIN tblMedicationDispensations ON tblMedicationPrescriptions.id = tblMedicationDispensations.prescription_id
				LEFT JOIN tblClients ON tblMedicationPrescriptions.client_id = tblClients.id
				LEFT JOIN tblChartSoap ON SUBSTR(chart_note, LOCATE('|', chart_note) + 1) = tblChartSoap.id
				LEFT JOIN tblPatientOwners ON tblMedicationPrescriptions.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.ID
				LEFT JOIN tblUsers ON tblChartSoap.seen_by = tblUsers.id
				LEFT JOIN items ON tblMedicationPrescriptions.item_id = items.id
			WHERE
				tblMedicationDispensations.signed = 0
				AND tblMedicationPrescriptions.chart_note LIKE '%Soap%'
				AND tblMedicationPrescriptions.removed = 0
				AND tblMedicationDispensations.removed = 0
				AND tblMedicationPrescriptions.location_id = :location11
				AND tblPatientOwners.primary = 1
				AND tblMedicationPrescriptions.deleted_at IS NULL
				AND tblMedicationDispensations.issue_date BETWEEN :beginDate11 AND :endDate11

			UNION ALL

			SELECT
				'Medication' AS title,
				'unsigned' AS status,
				tblMedicationPrescriptions.id,
				tblChartTreatment.seen_by AS user_id,
				tblMedicationPrescriptions.organization_id,
				tblMedicationPrescriptions.client_id,
				CONCAT(ifnull(items.name, ''), ' - ', ifnull(tblMedicationDispensations.note, ''), ' ', 'QTY:', ifnull(tblMedicationDispensations.qty, ''), ' Units:', ifnull(tblMedicationDispensations.units, '')) AS 'description',
				tblMedicationDispensations.issue_date AS due_date,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblMedicationPrescriptions
				INNER JOIN tblMedicationDispensations ON tblMedicationPrescriptions.id = tblMedicationDispensations.prescription_id
				LEFT JOIN tblClients ON tblMedicationPrescriptions.client_id = tblClients.id
				LEFT JOIN tblChartTreatment ON SUBSTR(chart_note, LOCATE('|', chart_note) + 1) = tblChartTreatment.id
				LEFT JOIN tblPatientOwners ON tblMedicationPrescriptions.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.ID
				LEFT JOIN tblUsers ON tblChartTreatment.seen_by = tblUsers.id
				LEFT JOIN items ON tblMedicationPrescriptions.item_id = items.id
			WHERE
				tblMedicationDispensations.signed = 0
				AND tblMedicationPrescriptions.chart_note LIKE '%Treatment%'
				AND tblMedicationPrescriptions.removed = 0
				AND tblMedicationDispensations.removed = 0
				AND tblMedicationPrescriptions.location_id = :location12
				AND tblPatientOwners.primary = 1
				AND tblMedicationPrescriptions.deleted_at IS NULL
				AND tblMedicationDispensations.issue_date BETWEEN :beginDate12 AND :endDate12

			UNION ALL

			SELECT
				tblUserTasks.id,
				tblUserTasks.user_id,
				tblUserTasks.organization_id,
				tblUserTasks.client_id,
				tblUserTasks.title,
				tblUserTasks.description,
				tblUserTasks.due_date,
				tblUserTasks.status,
				tblClients.first_name AS patient_name,
				tblUsers.first_name AS user_first_name,
				tblUsers.last_name AS user_last_name,
				tblPatientOwnerInformation.ID AS owner_id,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.middle_name AS owner_middle_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.phone AS owner_phone,
				tblPatientOwnerInformation.email AS owner_email
			FROM
				tblUserTasks
				LEFT JOIN tblUsers ON tblUserTasks.user_id = tblUsers.id
				LEFT JOIN tblClients ON tblUserTasks.client_id = tblClients.id
				LEFT JOIN tblPatientOwners ON tblUserTasks.client_id = tblPatientOwners.client_id
				LEFT JOIN tblPatientOwnerInformation ON tblPatientOwners.owner_id = tblPatientOwnerInformation.id
			WHERE
				tblUserTasks.status = 'pending'
				AND tblPatientOwners.primary = 1
				AND tblUserTasks.removed = 0
				AND DATE(CONVERT_TZ(tblUserTasks.due_date, 'UTC', :timeZone13)) BETWEEN :beginDate13 AND :endDate13
				",
			),
			$this->getQueryParameters(),
		);

		$location = Location::on($this->getConnectionName())
			->where("id", $this->getQueryParameters()["location1"])
			->with("subregion", "organization", "tz")
			->first();

		return response()->json([
			"toDoListItems" => $toDoListItems,
			"location1" => $location,
			"timeZone1" => $this->getQueryParameters()["timeZone1"],
			"beginDate1" => $this->getQueryParameters()["beginDate1"],
			"endDate1" => $this->getQueryParameters()["endDate1"],
		]);
	}

	public function buildParams(Request $request): array
	{
		return [
			"timeZone1" => $request->input("timeZone"),
			"timeZone2" => $request->input("timeZone"),
			"timeZone3" => $request->input("timeZone"),
			"timeZone4" => $request->input("timeZone"),
			"timeZone5" => $request->input("timeZone"),
			"timeZone6" => $request->input("timeZone"),
			"timeZone13" => $request->input("timeZone"),
			"beginDate1" => $request->input("beginDate"),
			"beginDate2" => $request->input("beginDate"),
			"beginDate3" => $request->input("beginDate"),
			"beginDate4" => $request->input("beginDate"),
			"beginDate5" => $request->input("beginDate"),
			"beginDate6" => $request->input("beginDate"),
			"beginDate7" => $request->input("beginDate"),
			"beginDate8" => $request->input("beginDate"),
			"beginDate9" => $request->input("beginDate"),
			"beginDate10" => $request->input("beginDate"),
			"beginDate11" => $request->input("beginDate"),
			"beginDate12" => $request->input("beginDate"),
			"beginDate13" => $request->input("beginDate"),
			"endDate1" => $request->input("endDate"),
			"endDate2" => $request->input("endDate"),
			"endDate3" => $request->input("endDate"),
			"endDate4" => $request->input("endDate"),
			"endDate5" => $request->input("endDate"),
			"endDate6" => $request->input("endDate"),
			"endDate7" => $request->input("endDate"),
			"endDate8" => $request->input("endDate"),
			"endDate9" => $request->input("endDate"),
			"endDate10" => $request->input("endDate"),
			"endDate11" => $request->input("endDate"),
			"endDate12" => $request->input("endDate"),
			"endDate13" => $request->input("endDate"),
			"location1" => $request->input("locations")[0],
			"location2" => $request->input("locations")[0],
			"location3" => $request->input("locations")[0],
			"location4" => $request->input("locations")[0],
			"location5" => $request->input("locations")[0],
			"location6" => $request->input("locations")[0],
			"location7" => $request->input("locations")[0],
			"location8" => $request->input("locations")[0],
			"location9" => $request->input("locations")[0],
			"location10" => $request->input("locations")[0],
			"location11" => $request->input("locations")[0],
			"location12" => $request->input("locations")[0],
		];
	}
}
