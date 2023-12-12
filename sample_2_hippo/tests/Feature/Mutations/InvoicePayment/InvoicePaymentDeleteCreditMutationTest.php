<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\InvoicePayment;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentDeleteCreditMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_credit_payment_can_be_deleted()
	{
		$payment = InvoicePayment::factory()->create();

		$query = '
      mutation InvoicePaymentDeleteAccountCredit($input: invoicePaymentDeleteInput!) {
				invoicePaymentDeleteAccountCredit(input: $input) {
					data {            
						id          
					}
				}
			}    
    ';
		$input = [
			"input" => [
				"invoicePayment" => (string) $payment->payment_id,
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response
			->assertJsonStructure([
				"data" => [
					"invoicePaymentDeleteAccountCredit" => [
						"data" => [
							"*" => [],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"invoicePaymentDeleteAccountCredit" => [
						"data" => [],
					],
				],
			]);
	}
}
