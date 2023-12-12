<?php

namespace Tests\Feature\Factories\Supplier;

use App\Models\Supplier;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class SupplierFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$supplier = Supplier::factory()->create();

		$this->assertDatabaseHas("suppliers", [
			"id" => $supplier->id,
			"company_name" => $supplier->company_name,
		]);
	}
}
