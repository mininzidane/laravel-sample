<?php

namespace Tests\Feature\Factories\InvoiceAppliedDiscount;

use App\Models\InvoiceAppliedDiscount;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceAppliedDiscountFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var InvoiceAppliedDiscount $model */
		$model = InvoiceAppliedDiscount::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"invoice_id" => $model->invoice_id,
			"discount_invoice_item_id" => $model->discount_invoice_item_id,
			"adjusted_invoice_item_id" => $model->adjusted_invoice_item_id,
			"amount_applied" => $model->amount_applied,
		]);
	}
}
