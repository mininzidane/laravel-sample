<?php

namespace Tests\Feature\Factories\ReceivingItem;

use App\Models\ReceivingItem;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReceivingItemFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var ReceivingItem $model */
		$model = ReceivingItem::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"receiving_id" => $model->receiving_id,
			"item_id" => $model->item_id,
			"line" => $model->line,
			"quantity" => $model->quantity,
			"comment" => $model->comment,
			"cost_price" => $model->cost_price,
			"discount_percentage" => $model->discount_percentage,
			"unit_price" => $model->unit_price,
		]);
	}
}
