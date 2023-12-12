<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Graphql\HippoGraphQLErrorCodes;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentIssueAccountCreditForOverpaymentMutation extends
	InvoicePaymentMutation
{
	protected $model = InvoicePayment::class;

	protected $permissionName = "Invoice Payments: Create";

	protected $attributes = [
		"name" => "InvoicePaymentIssueAccountCreditForOverpayment",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_ISSUE_ACCOUNT_CREDIT_FOR_OVERPAYMENT;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoice.exists" => "Please select a valid invoice",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type(
					"InvoicePaymentIssueAccountCreditForOverpaymentInput",
				),
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
		$invoice = Invoice::on($this->subdomainName)->findOrFail(
			$this->args["input"]["invoice"],
		);

		if ($invoice->amount_due >= 0) {
			throw new Exception(
				"The invoice balance must be negative in order to issue an account credit",
				HippoGraphQLErrorCodes::ACCOUNT_CREDIT_ISSUE_BALANCE_IS_POSITIVE,
			);
		}

		$paymentMethod = PaymentMethod::on($this->subdomainName)
			->where("process_type", "ISSUE_CREDIT")
			->firstOrFail();

		$location = Location::on($this->subdomainName)
			->where("id", $this->args["input"]["locationId"])
			->first();

		$paymentDetails = [
			"received_at" => Carbon::now(
				$location->tz->php_supported,
			)->toDateString(),
			"amount" => $invoice->amount_due,
			"owner_id" => $invoice->owner->id,
			"payment_method_id" => $paymentMethod->id,
			"credit_id" => null,
			"payment_platform_id" => null,
			"clearent_transaction_id" => null,
		];

		if (isset($this->args["input"]["paymentDate"])) {
			$paymentDetails["received_at"] =
				$this->args["input"]["paymentDate"];
		}

		$payment = Payment::on($this->subdomainName)->create($paymentDetails);

		$invoicePayment = InvoicePayment::on($this->subdomainName)->create([
			"invoice_id" => $invoice->id,
			"payment_id" => $payment->id,
			"amount_applied" => $invoice->amount_due,
		]);

		$accountCredit = Credit::on($this->subdomainName)->create([
			"type" => "Account Credit",
			"owner_id" => $invoice->owner->id,
			"number" =>
				"Invoice Overpayment: " .
				$invoice->id .
				"-" .
				$invoicePayment->id,
			"value" => abs($invoice->amount_due),
			"original_value" => abs($invoice->amount_due),
		]);

		$payment->credit_id = $accountCredit->id;
		$payment->save();

		$this->affectedId = $invoicePayment->id;

		return $this->model
			::on($this->subdomainName)
			->where($invoicePayment->getPrimaryKey(), $invoicePayment->id)
			->paginate(1);
	}
}
