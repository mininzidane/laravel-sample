<?php

namespace Tests\Feature\Factories\InventoryTransactionStatus;

use Tests\Helpers\TruncateDatabase;
use App\Models\InventoryTransactionStatus;
use Tests\Helpers\PassportSetupTestCase;

class InventoryTransactionStatusTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$status = InventoryTransactionStatus::factory()->create();

		$this->assertDatabaseHas("inventory_transaction_statuses", [
			"id" => $status->id,
			"name" => $status->name,
		]);
	}
}
