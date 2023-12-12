<?php

namespace Tests\Feature\Factories\LocationSetting;

use App\Models\LocationSetting;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class LocationSettingFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var LocationSetting $model */
		$model = LocationSetting::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"location_id" => $model->location_id,
			"setting_name" => $model->setting_name,
			"setting_value" => $model->setting_value,
		]);
	}
}
