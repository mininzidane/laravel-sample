<?php

namespace Tests\Feature\Factories\InventoryStatus;

use App\Models\InventoryStatus;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class InventoryStatusFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$status = InventoryStatus::factory()->create();

		$this->assertDatabaseHas("inventory_statuses", [
			"name" => $status->name,
		]);
	}
}
