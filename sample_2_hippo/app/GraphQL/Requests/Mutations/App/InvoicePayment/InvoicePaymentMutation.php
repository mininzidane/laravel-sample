<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\Invoice\InvoiceMutation;
use App\Models\InventoryTransactionStatus;
use App\Models\InvoiceStatus;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class InvoicePaymentMutation extends InvoiceMutation
{
	protected $model = InvoicePayment::class;

	protected function processAccountCreditPayment(
		$selectedCredit,
		$paymentMethod
	) {
		// set the amount tendered as either the input field amount tendered or the full credit value
		if (array_key_exists("useFullCreditAmount", $this->args["input"])) {
			$amountTendered = $selectedCredit->value;
		} elseif (array_key_exists("amountTendered", $this->args["input"])) {
			$amountTendered = $this->args["input"]["amountTendered"];
		} else {
			throw new \Exception("The payment amount is invalid");
		}

		if (round($amountTendered, 5) <= 0) {
			throw new \Exception(
				"A value greater than zero must be provided for the amount tendered",
				HippoGraphQLErrorCodes::NON_POSITIVE_PAYMENT_AMOUNT,
			);
		}

		if (round($amountTendered, 5) > $selectedCredit->value) {
			throw new \Exception(
				"The requested amount to apply is greater than the value of the credit",
			);
		}

		$invoices = $this->getInvoices(
			$this->args["input"]["invoiceIds"],
			array_key_exists("paymentOrder", $this->args["input"])
				? $this->args["input"]["paymentOrder"]
				: 0,
		);

		$bulkPayment = sizeOf($invoices) > 1 ? true : false;
		$invoicePayments = [];

		foreach ($invoices as $invoice) {
			if ($invoice->amountDue <= 0) {
				continue;
			}

			if ($amountTendered <= 0) {
				continue;
			}

			if (round($amountTendered, 2) >= round($invoice->amountDue, 2)) {
				$amountApplied = round($invoice->amountDue, 2);
				$invoiceSatisfied = true;
			} else {
				$amountApplied = round($amountTendered, 2);
				$invoiceSatisfied = false;
			}

			$amountTendered = $amountTendered - $amountApplied;

			$invoicePayments[] = [
				"invoiceId" => $invoice->id,
				"amountApplied" => $amountApplied,
				"invoiceSatisfied" => $invoiceSatisfied,
				"invoice" => $invoice,
			];
		}

		$totalAmountApplied = array_reduce(
			$invoicePayments,
			function ($total, $appliedPayment) {
				$total += $appliedPayment["amountApplied"];
				return $total;
			},
			0,
		);

		if ($totalAmountApplied === 0) {
			throw new \Exception(
				"This payment could not be applied as there is no outstanding balance",
			);
		}

		if ($totalAmountApplied > $selectedCredit->value) {
			throw new \Exception(
				"Something went wrong processing this payment",
			);
		}

		$selectedCredit->value = $selectedCredit->value - $totalAmountApplied;
		$selectedCredit->save();

		$location = Location::on($this->subdomainName)
			->where("id", $this->args["input"]["locationId"])
			->first();

		$paymentDetails = [
			"received_at" => Carbon::now(
				$location->tz->php_supported,
			)->toDateString(),
			"amount" => $totalAmountApplied,
			"owner_id" => $this->args["input"]["owner"],
			"payment_method_id" => $paymentMethod->id,
			"credit_id" => $selectedCredit->id,
			"payment_platform_id" => null,
			"clearent_transaction_id" => null,
		];

		if (isset($this->args["input"]["paymentDate"])) {
			$paymentDetails["received_at"] = Carbon::parse(
				$this->args["input"]["paymentDate"],
			)->toDateString();
		}

		$payment = Payment::on($this->subdomainName)->create($paymentDetails);

		foreach ($invoicePayments as $invoicePayment) {
			InvoicePayment::on($this->subdomainName)->create([
				"invoice_id" => $invoicePayment["invoiceId"],
				"amount_applied" => $invoicePayment["amountApplied"],
				"payment_id" => $payment->id,
			]);

			if ($bulkPayment && $invoicePayment["invoiceSatisfied"]) {
				$this->completeInvoice($invoicePayment["invoice"]);
			}
		}

		return $payment;
	}

	protected function completeInventoryTransactions($invoiceItem)
	{
		$completeInventoryTransactionStatus = InventoryTransactionStatus::on(
			$this->subdomainName,
		)
			->where("name", "Complete")
			->firstOrFail();

		foreach ($invoiceItem->inventoryTransactions as $inventoryTransaction) {
			$inventoryTransaction->status_id =
				$completeInventoryTransactionStatus->id;
			$inventoryTransaction->save();
		}
	}

	protected function completeInvoice($invoice)
	{
		$readyToComplete = true;

		foreach ($invoice->invoiceItems as $invoiceItem) {
			if ($invoiceItem->is_serialized && !$invoiceItem->serial_number) {
				$readyToComplete = false;
			}
		}

		if ($readyToComplete) {
			foreach ($invoice->invoiceItems as $invoiceItem) {
				if ($invoiceItem->hasInventory) {
					$this->completeInventoryTransactions($invoiceItem);
				}
			}

			$completeStatus = InvoiceStatus::on($this->subdomainName)
				->where("name", "Complete")
				->firstOrFail();

			$invoice->update([
				"status_id" => $completeStatus->id,
				"completed_at" => Carbon::now(),
				"active" => 0,
			]);
		}
	}
}
