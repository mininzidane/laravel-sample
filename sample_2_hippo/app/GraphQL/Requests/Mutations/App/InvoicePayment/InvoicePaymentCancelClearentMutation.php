<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\InvoicePayment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentCancelClearentMutation extends InvoicePaymentMutation
{
	protected $permissionName = "Invoice Payments: Delete";

	protected $attributes = [
		"name" => "InvoicePaymentCancelClearent",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_CANCEL_CLEARENT;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoicePaymentIds.required" =>
				"At least one invoice payment must be specified",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoicePaymentCancelClearentInput"),
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
		$invoicePayments = InvoicePayment::on($this->subdomainName)
			->whereIn("id", $this->args["input"]["invoicePaymentIds"])
			->get();

		foreach ($invoicePayments as $invoicePayment) {
			$invoicePayment->delete();
		}

		return InvoicePayment::on($this->subdomainName)
			->whereIn("id", $this->args["input"]["invoicePaymentIds"])
			->paginate(1);
	}
}
