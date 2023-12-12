<?php

namespace Tests\Feature\Factories\MucousMembraneStatus;

use Tests\Helpers\TruncateDatabase;
use App\Models\MucousMembraneStatus;
use Tests\Helpers\PassportSetupTestCase;

class MucousMembraneStatusFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$status = MucousMembraneStatus::factory()->create();

		$this->assertDatabaseHas($status->getTable(), [
			"id" => $status->id,
			"label" => $status->label,
			"abbr" => $status->abbr,
		]);
	}
}
