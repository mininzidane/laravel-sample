<?php

namespace App\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use App\Models\Owner;

class ClientStatements extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters([
			"location" => $request->input("locations")[0],
			"ownerId" => $request->input("ownerId"),
			"patientId" => $request->input("patientId"),
		]);

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$location = $this->getQueryParameters()["location"];

		$owners = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"SELECT DISTINCT
			  owners.id AS id,
			  owners.client_id,
			  CONCAT_WS(' ', first_name, last_name) AS full_name,
			  owners.address1,
			  owners.city,
			  states.code AS state,
			  owners.zip,
			(SELECT IFNULL(SUM(credits.value), 0)
			   FROM credits
			   WHERE credits.owner_id = owners.id
			   and credits.deleted_at is null) AS credits
			FROM tblPatientOwnerInformation AS owners
			LEFT JOIN tblSubRegions AS states
			  ON owners.state = states.id
			INNER JOIN invoices
			  ON owners.id = invoices.owner_id
			WHERE owners.deleted_at IS NULL
			  AND invoices.location_id = " .
					$this->getQueryParameters()["location"] .
					"
			  AND invoices.status_id = 1
			  AND invoices.completed_at IS NULL
			  AND invoices.total > 0
			ORDER BY
			  owners.last_name,
			  owners.first_name,
			  owners.id;",
			),
			$this->getQueryParameters(),
		);

		$otherOwners = DB::connection($this->getConnectionName())->select(
			DB::raw("SELECT DISTINCT
						other_owner_info.id,
						other_owner_info.client_id,
						other_owner_info.first_name,
						other_owner_info.last_name,
						other_owner_info.address1,
						other_owner_info.address2,
						other_owner_info.city,
						other_owner_info.zip,
						states.code AS state
					FROM tblPatientOwners AS patient
					INNER JOIN tblPatientOwnerInformation AS other_owner_info 
						ON patient.owner_id = other_owner_info.id
						and other_owner_info.deleted_at is null
					INNER JOIN tblSubRegions AS states ON other_owner_info.state = states.id
					WHERE 
						patient.primary = 0
						AND patient.deleted_at IS NULL
						AND patient.relationship_type = 'Owner';"),
			$this->getQueryParameters(),
		);

		$invoices = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"SELECT
			  invoices.id,
			  invoices.owner_id,
			  invoices.patient_id,
			  patients.first_name AS patient_name,
			  invoices.created_at,
			  (SELECT SUM(invoice_items.total)
			   FROM invoice_items
			   WHERE invoice_items.invoice_id = invoices.id
			     AND invoice_items.deleted_at IS NULL) AS subtotal,
			  (SELECT IFNULL(SUM(invoice_item_taxes.amount), 0)
			   FROM invoice_item_taxes
			   INNER JOIN invoice_items
				 ON invoice_item_taxes.invoice_item_id = invoice_items.id
			   WHERE invoice_items.invoice_id = invoices.id
			     AND invoice_item_taxes.deleted_at IS NULL
			     AND invoice_items.deleted_at IS NULL) AS taxes,
			  invoices.total AS total,
			  (SELECT IFNULL(SUM(invoice_payments.amount_applied), 0)
			   FROM invoice_payments
			   WHERE invoice_payments.invoice_id = invoices.id) AS totalPayments,
			  (SELECT invoices.total - IFNULL(SUM(invoice_payments.amount_applied), 0)
			   FROM invoice_payments
			   WHERE invoice_payments.invoice_id = invoices.id) AS amountDue
			FROM invoices
			INNER JOIN tblClients AS patients
			  ON invoices.patient_id = patients.id
			WHERE invoices.location_id = " .
					$this->getQueryParameters()["location"] .
					"
			  AND invoices.status_id = 1
			  AND invoices.completed_at IS NULL
			  AND invoices.total > 0
			  AND invoices.deleted_at IS NULL
			ORDER BY invoices.created_at;",
			),
			$this->getQueryParameters(),
		);

		$invoiceItems = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"SELECT
			  invoice_items.id,
			  invoice_items.invoice_id,
			  invoice_items.type_id,
			  invoice_items.name,
			  invoice_items.quantity,
			  invoice_items.total,
			  (SELECT IFNULL(SUM(invoice_item_taxes.amount), 0)
			   FROM invoice_item_taxes
			   WHERE invoice_item_taxes.invoice_item_id = invoice_items.id
		         AND invoice_item_taxes.deleted_at IS NULL) AS tax_total,
			  invoice_items.belongs_to_kit_id,
			  invoice_items.is_single_line_kit
			FROM invoice_items
			INNER JOIN invoices
			  ON invoice_items.invoice_id = invoices.id
			WHERE invoices.location_id = " .
					$this->getQueryParameters()["location"] .
					"
			  AND invoices.status_id = 1
			  AND invoices.completed_at IS NULL
			  AND invoices.total > 0
			  AND invoices.deleted_at IS NULL
			  AND invoice_items.deleted_at IS NULL
			ORDER BY
			  invoice_items.invoice_id,
			  invoice_items.line;",
			),
			$this->getQueryParameters(),
		);

		$invoicePayments = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"SELECT
			  invoice_payments.id,
			  invoice_payments.payment_id,
			  invoice_payments.invoice_id,
			  invoice_payments.amount_applied,
			  payments.payment_method_id,
			  payments.amount AS payment_amount,
			  payments.received_at,
			  payment_methods.`name` AS payment_method_name
			FROM invoice_payments
			INNER JOIN invoices
			  ON invoice_payments.invoice_id = invoices.id
			INNER JOIN payments
			  ON invoice_payments.payment_id = payments.id
			INNER JOIN payment_methods
			  ON payments.payment_method_id = payment_methods.id
			WHERE invoices.location_id = " .
					$this->getQueryParameters()["location"] .
					"
			  AND invoices.status_id = 1
			  AND invoices.completed_at IS NULL
			  AND invoices.total > 0
			  AND invoices.deleted_at IS NULL",
			),
			$this->getQueryParameters(),
		);

		$groupedInvoicePayments = [];
		foreach ($invoicePayments as $invoicePayment) {
			$groupedInvoicePayments[
				$invoicePayment->invoice_id
			][] = $invoicePayment;
		}

		$groupedInvoiceItems = [];
		foreach ($invoiceItems as $invoiceItem) {
			$groupedInvoiceItems[$invoiceItem->invoice_id][] = $invoiceItem;
		}

		$groupedInvoices = [];
		foreach ($invoices as $invoice) {
			$invoice->invoice_items = $groupedInvoiceItems[$invoice->id] ?? [];
			$invoice->invoice_payments =
				$groupedInvoicePayments[$invoice->id] ?? [];
			$groupedInvoices[$invoice->owner_id][] = $invoice;
		}

		foreach ($owners as $index => $owner) {
			$owners[$index]->invoices = $groupedInvoices[$owner->id] ?? [];
		}

		$location = Location::on($this->getConnectionName())
			->where("id", $this->getQueryParameters()["location"])
			->with("subregion", "organization", "tz")
			->first();

		return response()->json([
			"owners" => $owners,
			"otherOwners" => $otherOwners,
			"location" => $location,
		]);
	}
}
