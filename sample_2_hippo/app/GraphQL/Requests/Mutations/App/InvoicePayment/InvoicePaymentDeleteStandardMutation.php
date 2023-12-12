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

class InvoicePaymentDeleteStandardMutation extends InvoicePaymentMutation
{
	protected $permissionName = "Invoice Payments: Delete";

	protected $attributes = [
		"name" => "InvoicePaymentDeleteStandard",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_DELETE_STANDARD;

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
