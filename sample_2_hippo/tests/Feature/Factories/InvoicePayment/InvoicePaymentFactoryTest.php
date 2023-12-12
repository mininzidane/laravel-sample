<?php

namespace Tests\Feature\Factories\InvoicePayment;

use App\Models\InvoicePayment;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoicePaymentFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var InvoicePayment $model */
		$model = InvoicePayment::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"invoice_id" => $model->invoice_id,
			"payment_id" => $model->payment_id,
			"amount_applied" => $model->amount_applied,
		]);
	}
}
