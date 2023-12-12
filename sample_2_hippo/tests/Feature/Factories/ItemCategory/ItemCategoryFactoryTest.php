<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemCategory;

use App\Models\ItemCategory;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemCategoryFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var ItemCategory $itemCategory */
		$itemCategory = ItemCategory::factory()->create();

		$this->assertDatabaseHas($itemCategory->getTable(), [
			"id" => $itemCategory->id,
			"name" => $itemCategory->name,
		]);
	}
}
