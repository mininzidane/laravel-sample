<?php

namespace Tests\Feature\Factories\AccessLevel;

use App\Models\AccessLevel;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class AccessLevelFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$level = AccessLevel::factory()->create();

		$this->assertDatabaseHas("roles", [
			"id" => $level->id,
			"name" => $level->name,
			"guard_name" => $level->guard_name,
		]);
	}
}
