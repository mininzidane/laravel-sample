<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentInitializeClearentMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_clearent_can_be_initialized()
	{
		$invoice = Invoice::factory()->create([
			"total" => 5.0,
		]);
		$query = 'mutation InvoicePaymentInitializeClearent(
			$input: invoicePaymentInitializeClearentInput!) {
				invoicePaymentInitializeClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [
				"amountTendered" => 5.0,
				"invoiceIds" => [
					0 => (string) $invoice->id,
				],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentInitializeClearent" => [
					"data" => [
						"*" => [],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"invoicePaymentInitializeClearent" => [
					"data" => [
						[
							"id" => (string) $invoice->id,
						],
					],
				],
			],
		]);
	}

	public function test_clearent_initialization_cannot_refund_multiple_invoices()
	{
		$invoice1 = Invoice::factory()->create([
			"total" => 5.0,
		]);
		$invoice2 = Invoice::factory()->create([
			"total" => 50.0,
		]);
		$query = 'mutation InvoicePaymentInitializeClearent(
			$input: invoicePaymentInitializeClearentInput!) {
				invoicePaymentInitializeClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [
				"amountTendered" => -5.0,
				"invoiceIds" => [
					0 => (string) $invoice1->id,
					1 => (string) $invoice2->id,
				],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"A refund cannot be issued for multiple invoices.",
			$response->json("errors.*.errorMessage"),
		);
	}

	public function test_clearent_initialization_payment_amount_cannot_exceed_remaining_balance()
	{
		$invoice1 = Invoice::factory()->create([
			"total" => 5.0,
		]);
		$invoice2 = Invoice::factory()->create([
			"total" => 50.0,
		]);
		$query = 'mutation InvoicePaymentInitializeClearent(
			$input: invoicePaymentInitializeClearentInput!) {
				invoicePaymentInitializeClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [
				"amountTendered" => 75.0,
				"invoiceIds" => [
					0 => (string) $invoice1->id,
					1 => (string) $invoice2->id,
				],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"The credit card payment amount exceeds the remaining balance",
			$response->json("errors.*.errorMessage"),
		);
	}
}
