<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Owner;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentCreateAccountCreditMutationTest extends
	PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_account_credit_payment_can_be_created()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$owner = Owner::factory()->create();
		$credit = Credit::factory()->create([
			"type" => Credit::ACCOUNT_CREDIT_TYPE,
			"owner_id" => $owner->id,
			"original_value" => 1.0,
			"value" => 1.0,
		]);

		$query = '
      mutation InvoicePaymentCreateAccountCredit($input: invoicePaymentCreateAccountCreditInput!) {      
				invoicePaymentCreateAccountCredit(input: $input) {
					data {           
							id
					}
				}
			}    
    ';
		$input = [
			"input" => [
				"amountTendered" => 1.0,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
				"owner" => (string) $owner->id,
				"selectedCredit" => $credit->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentCreateAccountCredit" => [
					"data" => [
						"*" => [
							"id", // payment.id
						],
					],
				],
			],
		]);
		// Note response from request is non–deterministic payment id so can't assert exactly what it will be
		$ids = $response->json(
			"data.invoicePaymentCreateAccountCredit.data.*.id",
		);
		$this->assertGreaterThan(0, $ids[0]);
	}

	public function test_account_credit_payment_can_not_be_created_invalid_payment_method()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$owner = Owner::factory()->create();

		$query = '
      mutation InvoicePaymentCreateAccountCredit($input: invoicePaymentCreateAccountCreditInput!) {      
				invoicePaymentCreateAccountCredit(input: $input) {
					data {           
						invoice{
							id
						}
						amountApplied
					}
				}
			}    
    ';
		$input = [
			"input" => [
				"selectedCredit" => (string) 0xffff,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
				"owner" => (string) $owner->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"The selected input.selected credit is invalid.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_account_credit_payment_can_not_be_created_without_owner()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$credit = Credit::factory()->create([
			"type" => Credit::ACCOUNT_CREDIT_TYPE,
			"original_value" => 1.0,
			"value" => 1.0,
		]);

		$query = '
      mutation InvoicePaymentCreateAccountCredit($input: invoicePaymentCreateAccountCreditInput!) {      
				invoicePaymentCreateAccountCredit(input: $input) {
					data {           
						invoice{
							id
						}
						amountApplied
					}
				}
			}    
    ';
		$input = [
			"input" => [
				"selectedCredit" => $credit->id,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"The input.owner field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	/**
	 * This test will fail until input.owner.exists validation rule is implemented
	 */
	public function test_account_credit_payment_can_not_be_created_invalid_owner()
	{
		$this->markTestSkipped(
			"test_account_credit_payment_can_not_be_created_invalid_owner will fail until input.owner.exists validation rule is implemented",
		);
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$credit = Credit::factory()->create([
			"type" => Credit::ACCOUNT_CREDIT_TYPE,
		]);

		$query = '
      mutation InvoicePaymentCreateAccountCredit($input: invoicePaymentCreateAccountCreditInput!) {      
				invoicePaymentCreateAccountCredit(input: $input) {
					data {           
						invoice{
							id
						}
						amountApplied
					}
				}
			}    
    ';
		$input = [
			"input" => [
				"selectedCredit" => $credit->id,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
				"amountTendered" => 1.0,
				"owner" => 0777,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"Please select a valid owner to associate",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
