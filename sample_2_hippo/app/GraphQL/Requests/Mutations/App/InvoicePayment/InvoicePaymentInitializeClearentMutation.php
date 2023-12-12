<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\InvoicePayment;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentInitializeClearentMutation extends InvoicePaymentMutation
{
	protected $model = InvoicePayment::class;

	protected $permissionName = "Invoice Payments: Create";

	protected $attributes = [
		"name" => "InvoicePaymentInitializeClearent",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_INITIALIZE_CLEARENT;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoiceIds.required" =>
				"At least one invoice must be selected",
			//'input.amountTendered.min' => 'Please provide a positive amount greater than zero for the amount tendered',
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type(
					"InvoicePaymentInitializeClearentInput",
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
		$totalDue = 0;
		$lastInvoicePaymentId = null;

		$sortDirection = array_key_exists("paymentOrder", $this->args["input"])
			? $this->args["input"]["paymentOrder"]
			: 0;

		$invoices = $this->getInvoices(
			$this->args["input"]["invoiceIds"],
			$sortDirection,
		);

		if (
			$this->args["input"]["amountTendered"] < 0 &&
			sizeof($invoices) > 1
		) {
			throw new \Exception(
				"A refund cannot be issued for multiple invoices.",
				HippoGraphQLErrorCodes::REFUND_MULTIPLE_INVOICES,
			);
		}

		$invoicePaymentIds = [];

		foreach ($invoices as $invoice) {
			$invoicePayment = InvoicePayment::on($this->subdomainName)->create([
				"invoice_id" => $invoice->id,
				"payment_id" => null,
				"amount_applied" => null,
			]);

			$invoicePaymentIds[] = $invoicePayment->id;
			$lastInvoicePaymentId = $invoicePayment->id;
			$totalDue += $invoice->amountDue;
		}

		if ($this->args["input"]["amountTendered"] > $totalDue) {
			throw new Exception(
				"The credit card payment amount exceeds the remaining balance",
				HippoGraphQLErrorCodes::CREDIT_CARD_AMOUNT_DUE_EXCEEDED,
			);
		}

		$this->affectedId = $lastInvoicePaymentId;

		return InvoicePayment::on($this->subdomainName)
			->whereIn("id", $invoicePaymentIds)
			->paginate(100);
	}
}
