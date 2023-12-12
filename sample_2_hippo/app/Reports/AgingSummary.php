<?php

namespace App\Reports;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgingSummary extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters($this->buildParams($request));

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$location = $this->getQueryParameters()["location"];

		// Get a list of all unpaid open invoices for the location.
		$invoices = DB::connection($this->getConnectionName())
			->table("invoices")
			->select("invoices.id", "invoices.total")
			->where("invoices.location_id", $location)
			->where("invoices.status_id", Invoice::OPEN_STATUS)
			->whereNull("invoices.completed_at")
			->whereNull("invoices.deleted_at")
			->where("invoices.total", ">", 0)
			->get();

		// Convert to array for whereIn usage.
		$invoiceIds = array_map(function ($invoice) {
			return $invoice->id;
		}, $invoices->toArray());

		// Get payments that would be associated with these invoices.
		$payments = DB::connection($this->getConnectionName())
			->table("invoice_payments")
			->select(
				"invoice_payments.invoice_id",
				"invoice_payments.amount_applied",
			)
			->whereIn("invoice_payments.invoice_id", $invoiceIds)
			->get()
			->toArray();

		$unpaidInvoices = $invoices->filter(function ($invoice, $key) use (
			$payments
		) {
			// Our clients don't always change the status of Invoices to Complete even though they are paid in full.
			// Get payments have been made to the invoice.
			$invoicePayments = array_filter($payments, function ($payment) use (
				$invoice
			) {
				return $payment->invoice_id === $invoice->id;
			});

			// Add up the payments for this invoice.
			$totalPayments = array_sum(
				array_map(function ($payment) {
					return $payment->amount_applied;
				}, $invoicePayments),
			);

			// If the payments are less than the total of the invoice, add it to our array.
			return $totalPayments < $invoice->total;
		});

		// Convert to a flat array for whereIn usage.
		$unpaidInvoiceIds = array_merge(
			[],
			array_map(function ($invoice) {
				return $invoice->id;
			}, $unpaidInvoices->toArray()),
		);

		// Get IDs of the owners for the unpaid invoices.
		$unpaidInvoiceOwnerIds = DB::connection($this->getConnectionName())
			->table("invoices")
			->select("invoices.owner_id")
			->distinct("invoices.owner_id")
			->whereIn("invoices.id", $unpaidInvoiceIds)
			->get()
			->map(function ($invoice) {
				return $invoice->owner_id;
			})
			->toArray();

		// Get the data necessary for report rendering.
		$owners = Owner::on($this->getConnectionName())
			->with([
				"invoices" => function ($query) use ($location) {
					$query
						->where("location_id", $location)
						->where("status_id", 1)
						->where("completed_at", "=", null)
						->where("total", ">", 0);
				},
			])
			->whereIn("id", $unpaidInvoiceOwnerIds)
			->orderBy("last_name")
			->get();

		$location = Location::on($this->getConnectionName())
			->where("id", $this->getQueryParameters()["location"])
			->with("subregion", "organization", "tz")
			->first();

		return response()->json([
			"owners" => $owners,
			"location" => $location,
		]);
	}

	public function buildParams(Request $request): array
	{
		return [
			"location" => $request->input("locations")[0],
		];
	}
}
