<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentDispenseChangeMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_change_can_be_dispensed()
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

		$query = 'mutation InvoicePaymentDispenseChange(
			$input: invoicePaymentDispenseChangeInput!) {
				invoicePaymentDispenseChange (input: $input) {
					data {
						id
					}
				}
			}';
		$input = [
			"input" => [
				"invoice" => $invoice->id,
				"locationId" => $location->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentDispenseChange" => [
					"data" => [
						"*" => [],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"invoicePaymentDispenseChange" => [
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

	public function test_unknown_invoice_raises_error()
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

		$query = 'mutation InvoicePaymentDispenseChange(
			$input: invoicePaymentDispenseChangeInput!) {
				invoicePaymentDispenseChange (input: $input) {
					data {
						id
					}
				}
			}';
		$input = [
			"input" => [
				"invoice" => 0xffff,
				"locationId" => $location->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"Please select a valid invoice",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
