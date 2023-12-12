<?php

namespace Tests\Feature\Factories\Invoice;

use App\Models\Invoice;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Invoice $model */
		$model = Invoice::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"status_id" => $model->status_id,
			"location_id" => $model->location_id,
			"patient_id" => $model->patient_id,
			"owner_id" => $model->owner_id,
			"user_id" => $model->user_id,
			"active" => $model->active,
			"comment" => $model->comment,
			"print_comment" => $model->print_comment,
			"rounding" => $model->rounding,
			"is_taxable" => $model->is_taxable,
			"total" => $model->total,
		]);

		// Check that appends attribute is set
		$this->assertEquals(0, $model->totalPayments);
		$this->assertEquals(0, $model->subtotal);
		$this->assertEquals(0, $model->taxesTotal);
		$this->assertEquals("Invoice", $model->type);
		$this->assertEquals("Payment Information", $model->emailMessageType);
		$this->assertEquals(0, $model->emailMessage);
	}
}
