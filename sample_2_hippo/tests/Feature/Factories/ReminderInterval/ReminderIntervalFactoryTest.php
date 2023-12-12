<?php

namespace Tests\Feature\Factories\ReminderInterval;

use App\Models\ReminderInterval;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReminderIntervalFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var ReminderInterval $model */
		$model = ReminderInterval::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"code" => $model->code,
			"name" => $model->name,
		]);
	}
}
