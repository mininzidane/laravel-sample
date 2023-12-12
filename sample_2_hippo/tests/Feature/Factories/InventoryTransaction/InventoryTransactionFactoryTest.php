<?php

namespace Tests\Feature\Factories\InventoryTransaction;

use App\Models\InventoryTransaction;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InventoryTransactionFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var InventoryTransaction $model */
		$model = InventoryTransaction::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"inventory_id" => $model->inventory_id,
			"user_id" => $model->user_id,
			"invoice_item_id" => $model->invoice_item_id,
			"status_id" => $model->status_id,
			"quantity" => $model->quantity,
			"comment" => $model->comment,
			"is_shrink" => $model->is_shrink,
			"shrink_reason" => $model->shrink_reason,
		]);
	}
}
