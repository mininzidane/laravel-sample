<?php

namespace Tests\Feature\Factories\HydrationStatus;

use App\Models\HydrationStatus;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class HydrationStatusFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$status = HydrationStatus::factory()->create();

		$this->assertDatabaseHas("tblHydrationStatusOptions", [
			"id" => $status->id,
			"label" => $status->label,
			"abbr" => $status->abbr,
		]);
	}
}
