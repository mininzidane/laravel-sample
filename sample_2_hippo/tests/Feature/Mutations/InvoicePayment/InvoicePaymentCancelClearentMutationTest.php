<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Payment;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentCancelClearentMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_clearent_payment_can_be_cancelled()
	{
		$invoicePayment = InvoicePayment::factory()->create([
			"payment_id" => Payment::factory(),
			"invoice_id" => Invoice::factory(),
		]);
		$query = 'mutation invoicePaymentCancelClearent(
			$input: invoicePaymentCancelClearentInput!) {
				invoicePaymentCancelClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [
				"invoicePaymentIds" => [
					0 => (string) $invoicePayment->id,
				],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentCancelClearent" => [
					"data" => [
						"*" => [],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"invoicePaymentCancelClearent" => [
					"data" => [],
				],
			],
		]);
	}

	public function test_clearent_payment_cancel_fails_without_invoice_payment()
	{
		$invoicePayment = InvoicePayment::factory()->create([
			"payment_id" => Payment::factory(),
			"invoice_id" => Invoice::factory(),
		]);
		$query = 'mutation invoicePaymentCancelClearent(
			$input: invoicePaymentCancelClearentInput!) {
				invoicePaymentCancelClearent(input: $input) {data {id}}}';
		$input = [
			"input" => [
				"invoicePaymentIds" => [],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"At least one invoice payment must be specified",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
