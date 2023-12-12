<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Owner;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentCreateGiftCardMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_gift_card_payment_can_be_created()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$owner = Owner::factory()->create();
		$giftCard = Credit::factory()->create([
			"type" => Credit::GIFT_CARD_TYPE,
			"owner_id" => $owner->id,
			"original_value" => 1.0,
			"value" => 1.0,
		]);

		$query = '
      mutation InvoicePaymentCreateGiftCard($input: invoicePaymentCreateGiftCardInput!) {      
				invoicePaymentCreateGiftCard(input: $input) {
					data {           
							id
					}
				}
			}    
    ';
		$input = [
			"input" => [
				"giftCard" => (string) $giftCard->id,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
				"owner" => (string) $owner->id,
				"useFullCreditAmount" => true,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentCreateGiftCard" => [
					"data" => [
						"*" => [
							"id", // payment.id
						],
					],
				],
			],
		]);
		// Note response from request is non–deterministic payment id so can't assert exactly what it will be
		$ids = $response->json("data.invoicePaymentCreateGiftCard.data.*.id");
		$this->assertGreaterThan(0, $ids[0]);
	}

	public function test_gift_card_payment_can_not_be_created_invalid_payment_method()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$owner = Owner::factory()->create();
		$giftCard = Credit::factory()->create([
			"type" => Credit::GIFT_CARD_TYPE,
			"owner_id" => $owner->id,
			"original_value" => 1.0,
			"value" => 1.0,
		]);

		$query = '
      mutation InvoicePaymentCreateGiftCard($input: invoicePaymentCreateGiftCardInput!) {      
				invoicePaymentCreateGiftCard(input: $input) {
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
				"giftCard" => (string) 0xffff,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
				"owner" => (string) $owner->id,
				"useFullCreditAmount" => true,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"The selected input.gift card is invalid.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_gift_card_payment_can_not_be_created_without_owner()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();

		$giftCard = Credit::factory()->create([
			"type" => Credit::GIFT_CARD_TYPE,
			"original_value" => 1.0,
			"value" => 1.0,
		]);

		$query = '
      mutation InvoicePaymentCreateGiftCard($input: invoicePaymentCreateGiftCardInput!) {      
				invoicePaymentCreateGiftCard(input: $input) {
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
				"giftCard" => (string) 0xffff,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
				"useFullCreditAmount" => true,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"Please select an owner to associate with this payment",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_gift_card_payment_can_not_be_created_invalid_owner()
	{
		$this->markTestSkipped(
			"test_gift_card_payment_can_not_be_created_invalid_owner will fail until input.owner.exists validation rule is implemented",
		);
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();

		$giftCard = Credit::factory()->create([
			"type" => Credit::GIFT_CARD_TYPE,
			"original_value" => 1.0,
			"value" => 1.0,
		]);

		$query = '
      mutation InvoicePaymentCreateGiftCard($input: invoicePaymentCreateGiftCardInput!) {      
				invoicePaymentCreateGiftCard(input: $input) {
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
				"giftCard" => (string) $giftCard->id,
				"invoiceIds" => [(string) $invoice->id],
				"locationId" => (string) $location->id,
				"useFullCreditAmount" => true,
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
