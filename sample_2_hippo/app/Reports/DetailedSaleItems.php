<?php

namespace App\Reports;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailedSaleItems extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request))
			->setSalesStatusSql($request)
			->setSaleTypeSql($request)
			->setReplicaConnection($request);

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$results = DB::connection($this->getConnectionName())
			->table("invoice_items")
			->leftJoin(
				"item_categories",
				"item_categories.id",
				"=",
				"invoice_items.category_id",
			)
			->leftJoin("invoices", function ($join) {
				$join
					->on("invoices.id", "=", "invoice_items.invoice_id")
					->whereNull("invoices.deleted_at");
			})
			->leftJoin(
				"invoice_statuses",
				"invoice_statuses.id",
				"=",
				"invoices.status_id",
			)
			->leftJoin(
				"tblOrganizationLocations",
				"tblOrganizationLocations.id",
				"=",
				"invoices.location_id",
			)
			->leftJoin(
				"tblPatientOwnerInformation",
				"tblPatientOwnerInformation.id",
				"=",
				"invoices.owner_id",
			)
			->leftJoin(
				"tblClients",
				"tblClients.id",
				"=",
				"invoices.patient_id",
			)
			->leftJoin("items", "items.id", "=", "invoice_items.item_id")
			->leftJoin("invoice_item_taxes", function ($join) {
				$join
					->on(
						"invoice_item_taxes.invoice_item_id",
						"=",
						"invoice_items.id",
					)
					->whereNull("invoice_item_taxes.deleted_at");
			})
			->select([
				"invoice_items.invoice_id as invoice_id",
				"invoice_items.item_id as item_id",
				"invoice_items.line as item_line",
				"invoice_items.name as item_name",
				"invoice_items.serial_number as item_serial_number",
				"invoice_items.quantity as item_quantity",
				"invoice_items.discount_amount as item_discount_amount",
				"invoice_items.discount_percent as item_discount_percentage",
				"invoice_items.cost_price as item_cost_price",
				// The "total" stored is technically only the Subtotal.
				"invoice_items.total as item_subtotal",
				// "Price" is retail price without any discounts
				"invoice_items.price as item_price",
				"tblPatientOwnerInformation.id as owner_id",
				"tblPatientOwnerInformation.first_name as owner_first_name",
				"tblPatientOwnerInformation.last_name as owner_last_name",
				"tblClients.id as client_id",
				"tblClients.first_name as patient_first_name",
				"tblClients.last_name as patient_last_name",
				"item_categories.name as category_name",
				"items.description as item_description",
				DB::raw("SUM(invoice_item_taxes.amount) as tax_amount"),
				"invoice_statuses.name as invoice_status_name",
				"invoices.created_at as invoice_sale_date",
				"invoices.completed_at as invoice_completed_at",
				"tblOrganizationLocations.name as invoice_sale_location",
			])
			->where(
				"invoice_statuses.id",
				"=",
				$this->getQueryParameters()["saleStatus"],
			)
			// When searching for Complete invoices, we query dates between completed_at, otherwise we will use the created_at.
			->whereDateBetween(
				$this->getQueryParameters()["saleStatus"] ==
				Invoice::COMPLETE_STATUS
					? "invoices.completed_at"
					: "invoices.created_at",
				$this->getQueryParameters()["timeZone"],
				$this->getQueryParameters()["beginDate"],
				$this->getQueryParameters()["endDate"],
			)
			// When using Laravel's models this would happen automatically, but since we're using a query builder this has to be manually included.
			->whereNull("invoice_items.deleted_at")
			->whereIn(
				"invoices.location_id",
				explode(",", $this->getQueryParameters()["locations"]),
			)
			->groupBy("invoice_items.id")
			->get()
			// We can either execute raw queries to have the database handle these calculations or have PHP handle them after data is retrieved.
			// I've opted to have PHP handle it to avoid database slowdowns.
			->map(function ($result) {
				$result->owner_name =
					$result->owner_first_name . " " . $result->owner_last_name;
				$result->patient_name =
					$result->patient_first_name .
					" " .
					$result->patient_last_name;
				// Profit Calculation
				$result->item_profit = number_format(
					$result->item_subtotal -
						$result->item_quantity * $result->item_cost_price,
					2,
				);
				// Item Subtotal
				$result->item_no_tax_no_discount_total = number_format(
					$result->item_quantity * $result->item_price,
					2,
				);
				// Item Total
				$result->item_total = number_format(
					$result->item_subtotal + $result->tax_amount,
					2,
				);
				return $result;
			});

		return response()->json($results);
	}

	public function buildParams(Request $request): array
	{
		return [
			"locations" => implode(",", $request->input("locations")),
			"timeZone" => $request->input("timeZone"),
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
			"saleStatus" => $request->input("saleStatus"),
		];
	}
}
