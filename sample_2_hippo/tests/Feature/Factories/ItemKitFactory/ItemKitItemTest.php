<?php

namespace Tests\Feature\Factories\ItemKitFactory;

use App\Models\ItemKitItem;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemKitItemTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var ItemKitItem $model */
		$model = ItemKitItem::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"item_kit_id" => $model->item_kit_id,
			"item_id" => $model->item_id,
			"quantity" => $model->quantity,
		]);
	}
}
