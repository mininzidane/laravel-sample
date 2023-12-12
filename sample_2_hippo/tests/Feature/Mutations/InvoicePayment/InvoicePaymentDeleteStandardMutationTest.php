<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\InvoicePayment;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentDeleteStatdardMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_standard_payment_can_be_deleted()
	{
		$payment = InvoicePayment::factory()->create();

		$query = 'mutation InvoicePaymentDeleteStandard($input: invoicePaymentDeleteInput!) {
			invoicePaymentDeleteStandard(input: $input) {
				data {
					id
				}
			}
		}';
		$input = [
			"input" => [
				"invoicePayment" => $payment->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentDeleteStandard" => [
					"data" => [
						"*" => [],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"invoicePaymentDeleteStandard" => [
					"data" => [],
				],
			],
		]);
	}

	public function test_unknown_standard_payment_delete_raises_error()
	{
		$query = 'mutation InvoicePaymentDeleteStandard($input: invoicePaymentDeleteInput!) {
			invoicePaymentDeleteStandard(input: $input) {
				data {
					id
				}
			}
		}';
		$input = [
			"input" => [
				"invoicePayment" => 0xff,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$this->assertContains(
			"Please select a valid invoice payment",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
