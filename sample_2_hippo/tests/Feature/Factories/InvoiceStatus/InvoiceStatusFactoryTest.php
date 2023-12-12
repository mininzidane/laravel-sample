<?php

namespace Tests\Feature\Factories\InvoiceStatus;

use App\Models\InvoiceStatus;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class InvoiceStatusFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var InvoiceStatus $status */
		$status = InvoiceStatus::factory()->create();

		$this->assertDatabaseHas("invoice_statuses", [
			"id" => $status->id,
			"name" => $status->name,
		]);
	}
}
