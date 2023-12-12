<?php

namespace Tests\Feature\Mutations\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceBulkMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	/**
	 * @return void
	 * This test shows the float comparison operator in the InvoicePaymentCreateStandardMutation on line 139 has been fixed
	 */
	public function test_can_bulk_pay_and_close_multiple_invoices()
	{
		$invoice = Invoice::factory()->create([
			"total" => 5.2,
		]);
		$invoice2 = Invoice::factory()->create([
			"total" => 5.2,
		]);
		$invoice3 = Invoice::factory()->create([
			"total" => 5.2,
		]);

		$query = '
              mutation InvoicePaymentCreateStandard($input: invoicePaymentCreateStandardInput!) {
                invoicePaymentCreateStandard(input: $input) {
                    data {
                        id
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

		//assume there is only one id and the id is 1
		$variables = [
			"input" => [
				"invoiceIds" => [
					"$invoice->id",
					"$invoice2->id",
					"$invoice3->id",
				],
				"paymentMethod" => "1",
				"owner" => "$invoice->owner_id",
				"amountTendered" =>
					$invoice->total + $invoice2->total + $invoice3->total,
				"locationId" => "$invoice->location_id",
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"invoicePaymentCreateStandard" => [
					"data" => [
						"*" => [
							"id",
							"invoice" => [
								"id",
								"amountDue",
								"patient" => ["id"],
							],
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("invoices", [
			"id" => 1,
			"status_id" => 2,
			"completed_at" => "$this->carbonTestTime",
		]);
		$this->assertDatabaseHas("invoices", [
			"id" => 2,
			"status_id" => 2,
			"completed_at" => "$this->carbonTestTime",
		]);
		$this->assertDatabaseHas("invoices", [
			"id" => 3,
			"status_id" => 2,
			"completed_at" => "$this->carbonTestTime",
		]);
	}
}
