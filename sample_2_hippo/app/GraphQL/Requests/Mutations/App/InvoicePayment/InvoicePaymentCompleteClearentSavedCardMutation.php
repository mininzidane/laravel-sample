<?php

namespace App\GraphQL\Requests\Mutations\App\InvoicePayment;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\ClearentTransaction;
use App\Models\ClearentToken;
use App\Models\InvoicePayment;
use App\Models\Organization;
use App\Models\Payment;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Rebing\GraphQL\Support\Facades\GraphQL;
use function Aws\guzzle_major_version;

class InvoicePaymentCompleteClearentSavedCardMutation extends
	InvoicePaymentMutation
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
				"type" => GraphQL::type(
					"InvoicePaymentCompleteClearentSavedCardInput",
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
		$amountTendered = $this->args["input"]["amountTendered"];

		$invoicePayments = InvoicePayment::on($this->subdomainName)
			->whereIn("id", $this->args["input"]["invoicePaymentIds"])
			->orderBy("created_at", "desc")
			->get();

		$clearentTerminals =
			$invoicePayments[0]->invoice->location->clearentTerminals;

		if (sizeof($clearentTerminals) === 0) {
			throw new Exception(
				"At least one Clearent terminal must be configured for use at this location to process a saved card",
				HippoGraphQLErrorCodes::CLEARENT_TERMINAL_NOT_CONFIGURED,
			);
		}

		$selectedClearentTerminal = $clearentTerminals[0];

		$bulkPayment = sizeOf($invoicePayments) > 1;

		if ($amountTendered < 0 && $bulkPayment) {
			throw new Exception(
				"A refund cannot be issued for multiple invoices.",
				HippoGraphQLErrorCodes::REFUND_MULTIPLE_INVOICES,
			);
		}

		if ($amountTendered > 0) {
			$saleType = "SALE";
			$queryPath = "transactions/sale";
		} else {
			$saleType = "REFUND";
			$queryPath = "transactions/refund";
		}

		$clearentToken = ClearentToken::on($this->subdomainName)->findOrFail(
			$this->args["input"]["tokenId"],
		);

		$organization = Organization::on($this->subdomainName)->firstOrFail();

		$invoiceToken = $organization->id . "-" . $invoicePayments[0]->id;

		$requestBody = [
			"type" => $saleType,
			"amount" => number_format($amountTendered, 2, ".", ""),
			"card" => $clearentToken->card_token,
			"exp-date" => $clearentToken->expiration_date,
			"invoice" => $invoiceToken,
			"check-field" => "invoice",
		];

		$clearentTransactionResponse = Http::withHeaders([
			"api-key" => $selectedClearentTerminal->api_key,
			"accept" => "application/json",
			"content-type" => "application/json",
			"accept-encoding" => "gzip, deflate, br",
		])
			->post(Config::get("clearent.url") . $queryPath, $requestBody)
			->json();

		// If the payment fails, cancel associated invoice payments
		if ($clearentTransactionResponse["code"] != 200) {
			foreach ($invoicePayments as $invoicePayment) {
				$invoicePayment->delete();
			}

			return InvoicePayment::on($this->subdomainName)
				->whereIn("id", $this->args["input"]["invoicePaymentIds"])
				->paginate(1);
		}

		$paymentDetails = [
			"received_at" => Carbon::now()->toDateString(),
			"amount" => $amountTendered,
			"owner_id" => $this->args["input"]["owner"],
			"payment_method_id" => $this->args["input"]["paymentMethod"],
			"credit_id" => null,
			"payment_platform_id" => null,
		];

		if (isset($this->args["input"]["paymentDate"])) {
			$paymentDetails["received_at"] =
				$this->args["input"]["paymentDate"];
		}

		$payment = Payment::on($this->subdomainName)->create($paymentDetails);

		$userId = Auth::guard("api-subdomain-passport")->user()->id;

		$clearentPaymentDetails = [
			"payment_platform_id" => $this->args["input"]["paymentPlatform"],
			"clearent_terminal_id" => $selectedClearentTerminal->id,
			"terminal_id" =>
				$clearentTransactionResponse["payload"]["transaction"][
					"terminal-id"
				],
			"user_id" => $userId,
			"token_id" => $clearentToken->id,
			"request_id" =>
				$clearentTransactionResponse["payload"]["transaction"]["id"],
			"request_type" =>
				$clearentTransactionResponse["payload"]["payloadType"],
			"response_status" => $clearentTransactionResponse["code"],
			"card_type" =>
				$clearentTransactionResponse["payload"]["transaction"][
					"card-type"
				],
			"last_four_digits" =>
				$clearentTransactionResponse["payload"]["transaction"][
					"last-four"
				],
			"authorization_code" =>
				$clearentTransactionResponse["payload"]["transaction"][
					"authorization-code"
				],
			"request_body" => json_encode($requestBody),
			"response_body" => json_encode($clearentTransactionResponse),
			"platform_mode" => Config::get("clearent.mode"),
			"payment_id" => $payment->id,
			"old_transaction_id" => null,
		];

		$clearentTransaction = ClearentTransaction::on(
			$this->subdomainName,
		)->create($clearentPaymentDetails);

		$payment->clearent_transaction_id = $clearentTransaction->id;

		$payment->save();

		// Refunds can only be issued for non-bulk payments
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

		$maxInvoicePaymentIndex = sizeof($invoicePayments) - 1;

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
}
