<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\ClearentTransaction;
use App\Models\ClearentToken;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoicePaymentCompleteClearentMutation extends InvoicePaymentMutation
{
	protected $permissionName = "Invoice Payments: Update";

	protected $attributes = [
		"name" => "InvoicePaymentCompleteClearent",
		"model" => InvoicePayment::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_PAYMENT_COMPLETE_CLEARENT;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoicePaymentIds.required" =>
				"Please provide at least one invoice payment to update",
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
				"type" => GraphQL::type("InvoicePaymentCompleteClearentInput"),
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

		$location = Location::on($this->subdomainName)
			->where("id", $this->args["input"]["locationId"])
			->first();

		$paymentDetails = [
			"received_at" => Carbon::now(
				$location->tz->php_supported,
			)->toDateString(),
			"amount" => $amountTendered,
			"owner_id" => $this->args["input"]["owner"],
			"payment_method_id" => $this->args["input"]["paymentMethod"],
			"credit_id" => null,
			"payment_platform_id" => array_key_exists(
				"paymentPlatform",
				$this->args["input"],
			)
				? $this->args["input"]["paymentPlatform"]
				: null,
		];

		if (isset($this->args["input"]["paymentDate"])) {
			$paymentDetails["received_at"] =
				$this->args["input"]["paymentDate"];
		}

		$payment = Payment::on($this->subdomainName)->create($paymentDetails);

		$userId = Auth::guard("api-subdomain-passport")->user()->id;

		$response = json_decode($this->args["input"]["response"], false);

		$clearentTransaction = ClearentTransaction::on(
			$this->subdomainName,
		)->create([
			"payment_platform_id" => $this->args["input"]["paymentPlatform"],
			"clearent_terminal_id" => $this->args["input"]["clearentTerminal"],
			"terminal_id" => $this->args["input"]["terminalId"],
			"user_id" => $userId,
			"token_id" => $this->args["input"]["usedTokenId"] ?? null,
			"request_id" => $this->args["input"]["requestId"],
			"request_type" => $this->args["input"]["requestType"],
			"response_status" => $this->args["input"]["responseStatus"],
			"card_type" => $response->payload->transaction->{'card-type'},
			"last_four_digits" =>
				$response->payload->transaction->{'last-four'},
			"authorization_code" =>
				$response->payload->transaction->{'authorization-code'},
			"request_body" => $this->args["input"]["request"],
			"response_body" => $this->args["input"]["response"],
			"platform_mode" => $this->args["input"]["platformMode"],
			"payment_id" => $payment->id,
		]);

		$payment->clearent_transaction_id = $clearentTransaction->id;

		$payment->save();

		if (isset($this->args["input"]["tokenId"])) {
			$existingToken = ClearentToken::on($this->subdomainName)
				->where("card_token", "=", $this->args["input"]["tokenId"])
				->first();

			if (!$existingToken) {
				$tokenDetails = [
					"card_token" => $this->args["input"]["tokenId"],
					"name" => $this->args["input"]["tokenName"],
					"owner_id" => $this->args["input"]["owner"],
					"origin_transaction_id" => $clearentTransaction->id,
					"card_type" => $this->args["input"]["tokenCardType"],
					"last_four_digits" => $this->args["input"]["tokenLastFour"],
					"expiration_date" => $this->args["input"]["tokenExpDate"],
				];

				$token = ClearentToken::on($this->subdomainName)->create(
					$tokenDetails,
				);
			}
		}

		$sortDirection = array_key_exists("paymentOrder", $this->args["input"])
			? $this->args["input"]["paymentOrder"]
			: 0;

		$invoicePayments = $this->getInvoicePayments(
			$this->args["input"]["invoicePaymentIds"],
			$sortDirection,
		);

		$maxInvoicePaymentIndex = sizeof($invoicePayments) - 1;
		$bulkPayment = sizeOf($invoicePayments) > 1 ? true : false;

		if ($amountTendered < 0) {
			$invoicePayment = $invoicePayments[0];

			$invoicePayment->payment_id = $payment->id;
			$invoicePayment->amount_applied = $amountTendered;
			$invoicePayment->save();

			return $this->model
				::on($this->subdomainName)
				->where($invoicePayment->getPrimaryKey(), $invoicePayment->id)
				->paginate(1);
		}

		// for each invoice payment, which already has an invoice id set,
		foreach ($invoicePayments as $index => $invoicePayment) {
			if (
				round($amountTendered, 2) >=
					round($invoicePayment->invoice->amountDue, 2) &&
				$index !== $maxInvoicePaymentIndex
			) {
				$amountApplied = round($invoicePayment->invoice->amountDue, 2);
				$invoiceSatisfied = true;
			} else {
				$amountApplied = round($amountTendered, 2);
				$invoiceSatisfied =
					round($amountTendered, 2) ==
					round($invoicePayment->invoice->amountDue, 2);
			}

			$amountTendered = round($amountTendered, 2) - $amountApplied;

			$invoicePayment->payment_id = $payment->id;
			$invoicePayment->amount_applied = $amountApplied;
			$invoicePayment->save();

			if ($bulkPayment && $invoiceSatisfied) {
				$this->completeInvoice($invoicePayment->invoice);
			}
		}

		$this->affectedId = $invoicePayment->id;

		return $this->model
			::on($this->subdomainName)
			->where($invoicePayment->getPrimaryKey(), $invoicePayment->id)
			->paginate(1);
	}

	protected function getInvoicePayments($invoicePaymentIds, $sortDirection)
	{
		$invoicePaymentsQuery = InvoicePayment::on(
			$this->subdomainName,
		)->whereIn("id", $this->args["input"]["invoicePaymentIds"]);

		if ($sortDirection === 0) {
			$invoicePaymentsQuery->orderBy("created_at", "desc");
		} else {
			$invoicePaymentsQuery->orderBy("created_at", "asc");
		}

		return $invoicePaymentsQuery->get();
	}
}
