<?php

namespace Tests\Feature\Query\BulkPayment;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Owner;
use App\Models\Payment;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;
use Throwable;

class InvoiceIsBulkTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	/** @test
	 * @throws Throwable
	 */
	public function it_can_determine_if_it_has_a_bulk_payment()
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

		$payment = Payment::create([
			"owner_id" => $owner->id,
			"payment_method_id" => 1,
			"amount" => $invoice->total + $invoice2->total + $invoice3->total,
			"received_at" => now(),
			"is_bulk" => true,
		]);

		$invoice_payments = InvoicePayment::create([
			"invoice_id" => $invoice->id,
			"payment_id" => $payment->id,
			"amount_applied" => $invoice->total,
			"created_at" => now(),
			"updated_at" => now(),
		]);
		$invoice_payments2 = InvoicePayment::create([
			"invoice_id" => $invoice2->id,
			"payment_id" => $payment->id,
			"amount_applied" => $invoice2->total,
			"created_at" => now(),
			"updated_at" => now(),
		]);
		$invoice_payments3 = InvoicePayment::create([
			"invoice_id" => $invoice3->id,
			"payment_id" => $payment->id,
			"amount_applied" => $invoice3->total,
			"created_at" => now(),
			"updated_at" => now(),
		]);

		//now create a non bulk pay just to check
		$invoice4 = Invoice::factory()->create([
			"status_id" => 2,
			"location_id" => 1,
			"patient_id" => 1,
			"owner_id" => $owner->id,
			"user_id" => 1,
			"total" => 555.0,
		]);
		$payment2 = Payment::create([
			"owner_id" => $owner->id,
			"payment_method_id" => 1,
			"amount" => $invoice4,
			"received_at" => now(),
			"is_bulk" => false,
		]);
		$invoice_payments5 = InvoicePayment::create([
			"invoice_id" => $invoice3->id,
			"payment_id" => $payment->id,
			"amount_applied" => $invoice3->total,
			"created_at" => now(),
			"updated_at" => now(),
		]);

		//set status_id to 2 for the invoice table

		$query =
			'
            {
                invoices(limit: 10, page: 1, ownerId: ' .
			$owner->id .
			', invoiceStatus: 2, locationId: 1 ) {
                    current_page
                    per_page
                    total
                    data {
                        id
                        active
                        total
                        amountDue
                        totalPayments
                        isBulk
                        bulkPaymentId
                    }
                }
            }
            ';

		$response = $this->postGraphqlJson($query);

		$responseData = $response->decodeResponseJson();

		foreach ($responseData["data"]["invoices"]["data"] as $invoiceData) {
			// Get the corresponding invoice model for this invoice data
			$invoiceModel = Invoice::find($invoiceData["id"]);

			// Determine if the invoice should be a bulk invoice or not
			$isBulkInvoice = in_array($invoiceModel->id, [
				$invoice->id,
				$invoice2->id,
				$invoice3->id,
			]);

			//make sure bulk payments have is bulk true
			$this->assertEquals($invoiceData["isBulk"], $isBulkInvoice);

			//payment_id is all the same for bulk
			if ($isBulkInvoice) {
				$this->assertEquals(
					$invoiceData["bulkPaymentId"],
					$payment->id,
				);
			} else {
				$this->assertNull($invoiceData["bulkPaymentId"]);
			}
		}
	}
}
