<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemLocation;

use App\Models\ItemLocation;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemLocationFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var ItemLocation $model */
		$model = ItemLocation::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"item_id" => $model->item_id,
			"location_id" => $model->location_id,
		]);
	}
}
