<?php

namespace Tests\Feature\Factories\Inventory;

use App\Models\Inventory;
use App\Models\Receiving;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InventoryFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Inventory $model */
		$model = Inventory::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"item_id" => $model->item_id,
			"receiving_item_id" => $model->receiving_item_id,
			"location_id" => $model->location_id,
			"status_id" => $model->status_id,
			"lot_number" => $model->lot_number,
			"serial_number" => $model->serial_number,
			"expiration_date" => $model->expiration_date,
			"starting_quantity" => $model->starting_quantity,
			"remaining_quantity" => $model->remaining_quantity,
			"is_open" => $model->is_open,
			"opened_at" => $model->opened_at->format(self::DATETIME_FORMAT),
		]);

		// Check that appends attribute is set
		$this->assertEquals(0, $model->remaining);
		$this->assertEquals($model->item()->first()->name, $model->name);

		/** @var Receiving $receiving */
		$receiving = Receiving::on($model->getConnectionName())->find(
			$model->receivingItem->receiving_id,
		);
		$this->assertEquals($receiving->received_at, $model->receivedAt);
	}
}
