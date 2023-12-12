<?php

namespace Tests\Feature\Factories\InvoiceItemTax;

use App\Models\InvoiceItemTax;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceItemTaxFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var InvoiceItemTax $model */
		$model = InvoiceItemTax::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"invoice_item_id" => $model->invoice_item_id,
			"tax_id" => $model->tax_id,
			"name" => $model->name,
			"percent" => $model->percent,
			"amount" => $model->amount,
		]);
	}
}
