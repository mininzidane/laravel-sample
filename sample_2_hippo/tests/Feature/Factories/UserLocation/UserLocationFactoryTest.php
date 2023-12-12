<?php

namespace Tests\Feature\Factories\UserLocation;

use App\Models\UserLocation;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class UserLocationFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var UserLocation $model */
		$model = UserLocation::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"user_id" => $model->user_id,
			"location_id" => $model->location_id,
		]);
	}
}
