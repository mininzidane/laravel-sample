<?php

namespace Tests\Feature\Factories\State;

use App\Models\State;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class StateFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$state = State::factory()->create();

		$this->assertDatabaseHas("tblSubRegions", [
			"id" => $state->id,
			"name" => $state->name,
			"timezone" => $state->timezone,
			"iso" => $state->iso,
		]);
	}
}
