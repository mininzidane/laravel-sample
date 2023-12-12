<?php

namespace App\Reports;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Location;

class ControlledSubstanceLog extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request));

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$all_items = DB::connection($this->getConnectionName())->select(
			DB::raw("
			SELECT
				items.id AS item_key,
				items.name AS item_name,
				items.number,
				inventory.item_id,
				SUM(inventory.remaining_quantity) AS on_hand_quantity,
				suppliers.company_name AS manufacturer,
				item_categories.name AS item_category,
				:beginDate AS begin_date,
				:endDate AS end_date
				FROM
				inventory
				LEFT JOIN items ON items.id = inventory.item_id
					AND items.is_controlled_substance = 1
				LEFT JOIN suppliers ON items.manufacturer_id = suppliers.id
				LEFT JOIN item_categories ON items.category_id = item_categories.id
				left join item_locations on item_locations.item_id = items.id
				WHERE items.id = inventory.item_id
					and item_locations.location_id in (:location)
				AND items.deleted_at IS NULL
				AND inventory.deleted_at IS NULL
				GROUP BY
					items.id,
					items.name,
					items.number,
					inventory.item_id,
					suppliers.company_name,
					item_categories.name 
				ORDER BY
					items.name ASC
			"),
			$this->getQueryParameters(),
		);

		$invoice_items = DB::connection($this->getConnectionName())->select(
			DB::raw("
			SELECT
				invoices.id AS invoice_id,
				invoices.created_at,
				invoice_items.id AS invoice_item_id,
				invoice_items.quantity AS dispensed_quantity,
				invoice_items.item_id AS item_key,
				invoice_items.administered_date,
				invoice_statuses.name AS invoice_status,
				inventory.lot_number,
				inventory.expiration_date,
				tblClients.first_name AS patient_first_name,
				tblClients.last_name AS patient_last_name,
				tblPatientOwnerInformation.first_name AS owner_first_name,
				tblPatientOwnerInformation.last_name AS owner_last_name,
				tblPatientOwnerInformation.address1 AS owner_address1,
				tblPatientOwnerInformation.address2 AS owner_address2,
				tblPatientOwnerInformation.city AS owner_city,
				tblSubRegions.name AS owner_state,
				tblPatientOwnerInformation.zip AS owner_zip,
				tblPatientOwnerInformation.country AS owner_country,
				tblUsers.first_name AS provider_first_name,
				tblUsers.last_name AS provider_last_name
			FROM
				invoices
				LEFT JOIN invoice_items ON invoices.id = invoice_items.invoice_id
				LEFT JOIN invoice_statuses ON invoices.status_id = invoice_statuses.id
				LEFT JOIN tblClients ON invoices.patient_id = tblClients.id
				LEFT JOIN tblPatientOwnerInformation ON invoices.owner_id = tblPatientOwnerInformation.id
				LEFT JOIN tblSubRegions ON tblPatientOwnerInformation.state = tblSubRegions.id
				LEFT JOIN tblUsers ON invoice_items.provider_id = tblUsers.id
				LEFT JOIN inventory_transactions ON invoice_items.id = inventory_transactions.invoice_item_id
					AND inventory_transactions.status_id IN (1, 3)
				LEFT JOIN inventory ON inventory_transactions.inventory_id = inventory.id
			WHERE
				invoices.deleted_at IS NULL
				AND invoice_items.deleted_at IS NULL
				AND invoices.status_id IN(1, 2)
				AND invoices.location_id IN(:location)
				AND invoice_items.is_controlled_substance = 1
				AND invoice_items.administered_date BETWEEN :beginDate
				AND :endDate
			"),
			$this->getQueryParameters(),
		);

		$location = Location::on($this->getConnectionName())
			->where("id", $this->getQueryParameters()["location"])
			->with("subregion", "organization", "tz")
			->first();

		return response()->json([
			"all_items" => $all_items,
			"invoice_items" => $invoice_items,
			"location" => $location,
			"beginDate" => $this->getQueryParameters()["beginDate"],
			"endDate" => $this->getQueryParameters()["endDate"],
		]);
	}

	public function buildParams(Request $request): array
	{
		return [
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
			"location" => $request->input("locations")[0],
		];
	}
}
