<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemType;

use App\Models\ItemType;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemTypeFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var ItemType $itemType */
		$itemType = ItemType::factory()->create();

		$this->assertDatabaseHas($itemType->getTable(), [
			"id" => $itemType->id,
			"name" => $itemType->name,
			"process_inventory" => $itemType->process_inventory,
		]);
	}
}
