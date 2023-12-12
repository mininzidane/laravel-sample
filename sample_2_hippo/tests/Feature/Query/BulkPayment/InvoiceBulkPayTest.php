<?php
namespace Tests\Feature\Query\BulkPayment;

use App\Models\Invoice;
use App\Models\Owner;
use App\Models\Payment;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceBulkPayTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected $query = '
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
        }';
	public function test_when_multiple_invoices_are_paid_at_once_make_is_bulk_as_true()
	{
		$owner = Owner::factory()->create();
		$invoice = Invoice::factory()->create([
			"status_id" => 2,
			"location_id" => 1,
			"patient_id" => 1,
			"owner_id" => $owner->id,
			"user_id" => 1,
			"total" => 100.0,
		]);

		$invoice2 = Invoice::factory()->create([
			"status_id" => 2,
			"location_id" => 1,
			"patient_id" => 1,
			"owner_id" => $owner->id,
			"user_id" => 1,
			"total" => 200.0,
		]);

		$invoice3 = Invoice::factory()->create([
			"status_id" => 2,
			"location_id" => 1,
			"patient_id" => 1,
			"owner_id" => $owner->id,
			"user_id" => 1,
			"total" => 300.0,
		]);

		$variables = [
			"input" => [
				"invoiceIds" => [$invoice->id, $invoice2->id, $invoice3->id],
				"paymentMethod" => "1",
				"owner" => $owner->id,
				"amountTendered" => 600,
				"locationId" => "1",
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response->assertOk();

		$invoice->refresh();
		$invoice2->refresh();
		$invoice3->refresh();

		// Assert that the invoice status_id are set to 2
		$this->assertEquals(2, $invoice->status_id);
		$this->assertEquals(2, $invoice2->status_id);
		$this->assertEquals(2, $invoice3->status_id);

		// Get the payment
		$payment = Payment::first();

		$this->assertTrue((bool) $payment->is_bulk);
	}

	//now make sure non bulk payments are still correct
	public function test_when_one_invoice_is_paid_at_make_is_bulk_as_false()
	{
		$owner = Owner::factory()->create();
		$invoice = Invoice::factory()->create([
			"status_id" => 2,
			"location_id" => 1,
			"patient_id" => 1,
			"owner_id" => $owner->id,
			"user_id" => 1,
			"total" => 100.0,
		]);

		$variables = [
			"input" => [
				"invoiceIds" => [$invoice->id],
				"paymentMethod" => "1",
				"owner" => $owner->id,
				"amountTendered" => 600,
				"locationId" => "1",
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response->assertOk();

		$invoice->refresh();

		// Assert that the invoice status_id are set to 2
		$this->assertEquals(2, $invoice->status_id);

		// Get the payment
		$payment = Payment::first();

		$this->assertFalse((bool) $payment->is_bulk);
	}
}
