<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\Credit;
use App\Models\InvoicePayment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentCreateGiftCardMutation extends InvoicePaymentMutation
{
	protected $permissionName = "Invoice Payments: Create";

	protected $attributes = [
		"name" => "InvoicePaymentGiftCardCreate",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_CREATE_GIFT_CARD;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.paymentMethod.exists" =>
				"Please select a valid payment method",
			"input.paymentPlatform.exists" =>
				"Please select a valid payment platform",
			"input.owner.exists" => "Please select a valid owner to associate",
			"input.owner.required" =>
				"Please select an owner to associate with this payment",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoicePaymentCreateGiftCardInput"),
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
		$selectedCredit = Credit::on($this->subdomainName)->findOrFail(
			$this->args["input"]["giftCard"],
		);

		if (!$selectedCredit) {
			throw new Exception("The Gift Card Number is Invalid");
		}

		if ($selectedCredit->value <= 0) {
			throw new Exception(
				"The selected gift card has no remaining balance",
			);
		}

		$giftCardPaymentMethod = PaymentMethod::on($this->subdomainName)
			->where("name", "Gift Card")
			->firstOrFail();

		$payment = $this->processAccountCreditPayment(
			$selectedCredit,
			$giftCardPaymentMethod,
		);

		$this->affectedId = $payment->id;

		return Payment::on($this->subdomainName)
			->where("id", $payment->id)
			->paginate(1);
	}
}
