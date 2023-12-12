<?php

namespace Tests\Feature\Factories\SchedulerSettings;

use App\Models\SchedulerSettings;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class SchedulerSettingsFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var SchedulerSettings $model */
		$model = SchedulerSettings::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"organization_id" => $model->organization_id,
			"start_time" => $model->start_time,
			"end_time" => $model->end_time,
			"unit" => $model->unit,
			"max_duration" => $model->max_duration,
		]);
	}
}
