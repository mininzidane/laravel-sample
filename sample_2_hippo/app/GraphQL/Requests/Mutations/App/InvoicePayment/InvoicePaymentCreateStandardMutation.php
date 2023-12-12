<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Closure;
use Illuminate\Support\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentCreateStandardMutation extends InvoicePaymentMutation
{
	protected $permissionName = "Invoice Payments: Create";

	protected $attributes = [
		"name" => "InvoicePaymentStandardCreate",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_CREATE_STANDARD;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoiceIds.exists" =>
				"Please select at least one valid invoice",
			"input.paymentMethod.exists" =>
				"Please select a valid payment method",
			"input.owner.exists" => "Please select a valid owner to associate",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoicePaymentCreateStandardInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$amountTendered = $this->args["input"]["amountTendered"];

		$invoices = $this->getInvoices(
			$this->args["input"]["invoiceIds"],
			array_key_exists("paymentOrder", $this->args["input"])
				? $this->args["input"]["paymentOrder"]
				: 0,
		);

		if ($amountTendered < 0 && sizeof($invoices) > 1) {
			throw new \Exception(
				"A refund cannot be issued for multiple invoices.",
				HippoGraphQLErrorCodes::REFUND_MULTIPLE_INVOICES,
			);
		}

		$location = Location::on($this->subdomainName)
			->where("id", $this->args["input"]["locationId"])
			->first();

		$bulkPayment = sizeOf($invoices) > 1;

		$paymentDetails = [
			"received_at" => Carbon::now(
				$location->tz->php_supported,
			)->toDateString(),
			"amount" => $amountTendered,
			"owner_id" => $this->args["input"]["owner"],
			"payment_method_id" => $this->args["input"]["paymentMethod"],
			"credit_id" => null,
			"payment_platform_id" => null,
			"is_bulk" => $bulkPayment,
		];

		if (array_key_exists("paymentDate", $this->args["input"])) {
			$paymentDetails["received_at"] = Carbon::parse(
				$this->args["input"]["paymentDate"],
			)->toDateString();
		}

		$payment = Payment::on($this->subdomainName)->create($paymentDetails);

		$maxInvoiceIndex = sizeof($invoices) - 1;

		$lastInvoicePayment = null;

		if ($amountTendered < 0) {
			$lastInvoicePayment = $this->processPaymentForInvoice(
				$invoices[0],
				$payment,
				$amountTendered,
			);

			$this->affectedId = $lastInvoicePayment->id ?? $this->affectedId;

			return InvoicePayment::on($this->subdomainName)
				->where("id", $lastInvoicePayment->id)
				->paginate(1);
		}

		foreach ($invoices as $index => $invoice) {
			if ($amountTendered === 0) {
				continue;
			}

			// if the amount tendered is more than is required for the current invoice
			// and the current invoice isn't the last one, apply only the amount required to zero the invoice
			if (
				$amountTendered > $invoice->amountDue &&
				$index !== $maxInvoiceIndex
			) {
				$amountApplied = $invoice->amountDue;
				$invoiceSatisfied = true;
			} else {
				// but if it is the last invoice, apply all remaining tendered
				// but if the amount tendered is less than the amount due for that invoice, apply all tendered
				$amountApplied = $amountTendered;
				$invoiceSatisfied =
					round($amountTendered, 2) == round($invoice->amountDue, 2);
			}

			$amountTendered = $amountTendered - $amountApplied;

			$lastInvoicePayment = $this->processPaymentForInvoice(
				$invoice,
				$payment,
				$amountApplied,
				$invoiceSatisfied,
				$bulkPayment,
			);

			$this->affectedId = $lastInvoicePayment->id ?? $this->affectedId;
		}

		return InvoicePayment::on($this->subdomainName)
			->where("id", $lastInvoicePayment->id)
			->paginate(1);
	}

	public function processPaymentForInvoice(
		$invoice,
		$payment,
		$amountTendered,
		$invoiceSatisfied = false,
		$bulkPayment = false
	) {
		$payment = InvoicePayment::on($this->subdomainName)->create([
			"invoice_id" => $invoice->id,
			"payment_id" => $payment->id,
			"amount_applied" => $amountTendered,
		]);

		if ($bulkPayment && $invoiceSatisfied) {
			$this->completeInvoice($invoice);
		}

		return $payment;
	}
}
