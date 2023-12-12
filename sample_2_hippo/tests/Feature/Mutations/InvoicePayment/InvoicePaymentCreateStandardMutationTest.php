<?php

namespace Tests\Feature\Mutations\InvoicePayment;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Owner;
use App\Models\PaymentMethod;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentCreateStandardMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_standard_payment_can_be_created()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$owner = Owner::factory()->create();
		$paymentMethod = PaymentMethod::factory()->create();
		$paymentAmount = 10.0;

		$query = '
      mutation InvoicePaymentCreateStandard($input: invoicePaymentCreateStandardInput!) {
        invoicePaymentCreateStandard(input: $input) {
          data {
            
            invoice {
              id
              amountDue
              patient {
                id
              }
            }
          }
        }
      }
    ';
		$input = [
			"input" => [
				"amountTendered" => $paymentAmount,
				"invoiceIds" => [$invoice->id],
				"locationId" => $location->id,
				"owner" => $owner->id,
				"paymentMethod" => (string) $paymentMethod->id,
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);

		$response
			->assertJsonStructure([
				"data" => [
					"invoicePaymentCreateStandard" => [
						"data" => [
							"*" => [
								"invoice" => [
									"id",
									"amountDue",
									"patient" => ["id"],
								],
							],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"invoicePaymentCreateStandard" => [
						"data" => [
							[
								"invoice" => [
									"id" => (string) $invoice->id,
									"amountDue" =>
										(float) $invoice->total -
										$paymentAmount,
									"patient" => [
										"id" => (string) $invoice->patient_id,
									],
								],
							],
						],
					],
				],
			]);
	}
}
