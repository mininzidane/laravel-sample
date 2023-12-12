<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\InvoicePayment;
use App\Models\Payment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentDeleteCreditMutation extends InvoicePaymentMutation
{
	protected $permissionName = "Invoice Payments: Delete";

	protected $attributes = [
		"name" => "InvoicePaymentDelete",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_DELETE_ACCOUNT_CREDIT;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoicePayment.exists" =>
				"Please select a valid invoice payment",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoicePaymentDeleteInput"),
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
		$invoicePaymentToDelete = InvoicePayment::on(
			$this->subdomainName,
		)->findOrFail($this->args["input"]["invoicePayment"]);

		if (
			in_array($invoicePaymentToDelete->payment->paymentMethod->name, [
				"Account Credit",
				"Gift Card",
			]) &&
			$invoicePaymentToDelete->payment->credit
		) {
			$invoicePaymentToDelete->payment->credit->value +=
				$invoicePaymentToDelete->payment->amount;
			$invoicePaymentToDelete->push();
		}

		$paymentId = $invoicePaymentToDelete->payment->id;
		$invoice = $invoicePaymentToDelete->invoice;

		$invoicePaymentToDelete->delete();

		$paymentToDelete = Payment::on($this->subdomainName)->findOrFail(
			$paymentId,
		);
		$paymentToDelete->delete();

		$this->recalculateInvoiceTotal($invoice);

		$this->affectedId = $this->args["input"]["invoicePayment"];

		return InvoicePayment::on($this->subdomainName)
			->where("id", $this->args["input"]["invoicePayment"])
			->paginate(1);
	}
}
