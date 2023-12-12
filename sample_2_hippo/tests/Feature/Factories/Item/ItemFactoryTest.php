<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Item;

use App\Models\Item;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Item $item */
		$item = Item::factory()->create();

		$this->assertDatabaseHas($item->getTable(), [
			"id" => $item->id,
			"name" => $item->name,
			"number" => $item->number,
			"cost_price" => $item->cost_price,
			"unit_price" => $item->unit_price,
		]);
	}
}
