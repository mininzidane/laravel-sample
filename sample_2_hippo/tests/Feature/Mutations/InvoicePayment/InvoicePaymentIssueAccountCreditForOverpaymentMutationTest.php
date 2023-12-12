<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentIssueAccountCreditForOverpaymentMutationTest extends
	PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_account_can_be_credited()
	{
		$location = Location::factory()->create();
		$payment = Payment::factory()->create([
			"amount" => 100.0,
		]);
		$invoice = Invoice::factory()->create([
			"total" => 99.99,
		]);
		$invoicePayment = InvoicePayment::factory()->create([
			"invoice_id" => $invoice->id,
			"payment_id" => $payment->id,
			"amount_applied" => 100.0,
		]);

		$query = 'mutation InvoicePaymentIssueAccountCreditForOverpayment(
			$input: invoicePaymentIssueAccountCreditForOverpaymentInput) {
				invoicePaymentIssueAccountCreditForOverpayment(input: $input) {
					data {id}
				}
		}';
		$input = [
			"input" => [
				"invoice" => (string) $invoice->id,
				"locationId" => (string) $location->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentIssueAccountCreditForOverpayment" => [
					"data" => [
						"*" => [],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"invoicePaymentIssueAccountCreditForOverpayment" => [
					"data" => [
						[
							// Why this is + 1? When an overage payment is applied to an invoice
							// a new InvoicePayment is created with the outstanding amount.
							"id" => (string) ($invoicePayment->id + 1),
						],
					],
				],
			],
		]);
	}

	public function test_invoice_with_positive_balance_raises_error()
	{
		$location = Location::factory()->create();
		$payment = Payment::factory()->create([
			"amount" => 50.0,
		]);
		$invoice = Invoice::factory()->create([
			"total" => 99.99,
		]);
		$invoicePayment = InvoicePayment::factory()->create([
			"invoice_id" => $invoice->id,
			"payment_id" => $payment->id,
			"amount_applied" => 50.0,
		]);

		$query = 'mutation InvoicePaymentIssueAccountCreditForOverpayment(
			$input: invoicePaymentIssueAccountCreditForOverpaymentInput) {
				invoicePaymentIssueAccountCreditForOverpayment(input: $input) {
					data {id}
				}
		}';
		$input = [
			"input" => [
				"invoice" => (string) $invoice->id,
				"locationId" => (string) $location->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"The invoice balance must be negative in order to issue an account credit",
			$response->json("errors.*.errorMessage"),
		);
	}
}
