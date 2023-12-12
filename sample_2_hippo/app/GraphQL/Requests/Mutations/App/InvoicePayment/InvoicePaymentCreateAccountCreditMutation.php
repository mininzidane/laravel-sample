<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Credit;
use App\Models\InvoicePayment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentCreateAccountCreditMutation extends InvoicePaymentMutation
{
	protected $permissionName = "Invoice Payments: Create";

	protected $attributes = [
		"name" => "InvoicePaymentAccountCreditCreate",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_CREATE_ACCOUNT_CREDIT;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoice.exists" => "Please select a valid invoice",
			"input.paymentMethod.exists" =>
				"Please select a valid payment method",
			"input.paymentPlatform.exists" =>
				"Please select a valid payment platform",
			"input.owner.exists" => "Please select a valid owner to associate",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type(
					"InvoicePaymentCreateAccountCreditInput",
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
		if (!array_key_exists("selectedCredit", $this->args["input"])) {
			throw new Exception(
				"A credit must be supplied for use with account credit payments",
				HippoGraphQLErrorCodes::ACCOUNT_CREDIT_NOT_SELECTED,
			);
		}

		$selectedCredit = Credit::on($this->subdomainName)->findOrFail(
			$this->args["input"]["selectedCredit"],
		);

		$accountCreditPaymentMethod = PaymentMethod::on($this->subdomainName)
			->where("name", "Account Credit")
			->firstOrFail();

		$payment = $this->processAccountCreditPayment(
			$selectedCredit,
			$accountCreditPaymentMethod,
		);

		$this->affectedId = $payment->id;

		return Payment::on($this->subdomainName)
			->where("id", $payment->id)
			->paginate(1);
	}
}
