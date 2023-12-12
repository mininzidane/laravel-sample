<?php

namespace App\Reports;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Location;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemHistory extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters([
			"locations" => $request->input("locations"),
			"beginDate" => $request->input("beginDate"),
			"endDate" => $request->input("endDate"),
			"detailId" => $request->input("detailId"),
		]);
		parent::__construct();
	}

	public function generateReportData()
	{
		$location = Location::on($this->getConnectionName())
			->select(["timezone", "name"])
			->where("id", $this->getQueryParameters()["locations"][0])
			->first();

		if (!$location) {
			return response()->json(
				"Failed to find Location with ID of {$this->getQueryParameters()["locations"][0]}.",
				400,
			);
		}

		$tz = Timezone::on($this->getConnectionName())
			->select(["php_supported"])
			->where("id", $location->timezone)
			->first();

		if (!$tz) {
			return response()->json(
				"Location with ID of {$this->getQueryParameters()["locations"][0]} has no associated Timezone.",
				400,
			);
		}

		$item = Item::on($this->getConnectionName())
			->with("Category")
			->where("id", "=", $this->getQueryParameters()["detailId"])
			->firstOrFail()
			->only(["name", "id", "number", "description", "Category"]);

		$itemHistory = DB::connection($this->getConnectionName())
			->table("items")
			->join("invoice_items", "invoice_items.item_id", "=", "items.id")
			->join("invoices", "invoices.id", "=", "invoice_items.invoice_id")
			->leftJoin(
				"item_categories",
				"item_categories.id",
				"=",
				"items.category_id",
			)
			->leftJoin(
				"invoice_statuses",
				"invoice_statuses.id",
				"=",
				"invoices.status_id",
			)
			->leftJoin(
				"tblClients",
				"tblClients.id",
				"=",
				"invoices.patient_id",
			)
			->leftJoin(
				"tblPatientOwnerInformation",
				"tblPatientOwnerInformation.id",
				"=",
				"invoices.owner_id",
			)
			->leftJoin(
				"tblUsers",
				"tblUsers.id",
				"=",
				"invoice_items.provider_id",
			)
			->leftJoin(
				"inventory_transactions",
				"inventory_transactions.invoice_item_id",
				"=",
				"invoice_items.id",
			)
			->leftJoin(
				"inventory",
				"inventory.id",
				"=",
				"inventory_transactions.inventory_id",
			)
			->select([
				"invoice_items.quantity as item_quantity",
				"invoice_items.price as item_price",
				"invoice_items.total as item_total",
				"invoice_items.discount_amount as item_discount_amount",
				"invoice_items.dispensing_fee as item_dispensing_fee",
				"invoice_items.cost_price as item_cost_price",
				"invoices.id as invoice_id",
				"invoice_statuses.name as invoice_status",
				"invoices.created_at as invoice_created_at",
				"tblClients.first_name as patient_first_name",
				"tblClients.last_name as patient_last_name",
				"tblPatientOwnerInformation.first_name as owner_first_name",
				"tblPatientOwnerInformation.last_name as owner_last_name",
				"tblUsers.id as provider_id",
				"tblUsers.first_name as provider_first_name",
				"tblUsers.last_name as provider_last_name",
				DB::raw(
					"GROUP_CONCAT(DISTINCT inventory.lot_number ORDER BY inventory_transactions.id SEPARATOR ', ') as item_lot_number",
				),
				DB::raw(
					"GROUP_CONCAT(DISTINCT inventory.expiration_date ORDER BY inventory_transactions.id SEPARATOR ', ') as item_expiration_date",
				),
			])
			->where("items.id", "=", $this->getQueryParameters()["detailId"])
			->where(
				"invoices.location_id",
				"=",
				$this->getQueryParameters()["locations"][0],
			)
			// Only include Open and Complete invoices.
			->whereIn("invoices.status_id", [
				Invoice::OPEN_STATUS,
				Invoice::COMPLETE_STATUS,
			])
			->whereRaw(
				"DATE(CONVERT_TZ(invoices.created_at, 'UTC', ?)) BETWEEN ? AND ?",
				[
					$tz->php_supported,
					$this->getQueryParameters()["beginDate"],
					$this->getQueryParameters()["endDate"],
				],
			)
			->whereNull("invoice_items.deleted_at")
			->groupBy("invoice_items.id")
			->get();

		$totals = [
			"totalSold" => array_sum(
				$itemHistory
					->map(function ($ih) {
						return $ih->item_quantity;
					})
					->toArray(),
			),
			"totalRetailValue" => array_sum(
				$itemHistory
					->map(function ($ih) {
						return $ih->item_total;
					})
					->toArray(),
			),
			"totalReceivedCost" => array_sum(
				$itemHistory
					->map(function ($ih) {
						return $ih->item_quantity * $ih->item_cost_price;
					})
					->toArray(),
			),
			"totalDiscounts" => array_sum(
				$itemHistory
					->map(function ($ih) {
						return $ih->item_discount_amount;
					})
					->toArray(),
			),
			"totalDispensingFees" => array_sum(
				$itemHistory
					->map(function ($ih) {
						return $ih->item_dispensing_fee;
					})
					->toArray(),
			),
			"totalItemsReceived" => DB::connection($this->getConnectionName())
				->table("receiving_items")
				->join(
					"receivings",
					"receivings.id",
					"=",
					"receiving_items.receiving_id",
				)
				->where(
					"receiving_items.item_id",
					"=",
					$this->getQueryParameters()["detailId"],
				)
				->whereDateBetween(
					"receivings.received_at",
					$tz->php_supported,
					$this->getQueryParameters()["beginDate"],
					$this->getQueryParameters()["endDate"],
				)
				->sum("receiving_items.quantity"),
		];

		return response()->json([
			"location" => $location->name,
			"totals" => $totals,
			"item" => $item,
			"itemHistory" => $itemHistory,
			"beginDate" => $this->getQueryParameters()["beginDate"],
			"endDate" => $this->getQueryParameters()["endDate"],
		]);
	}
}
