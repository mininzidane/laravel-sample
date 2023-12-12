<?php

namespace Tests\Feature\Factories\LabTest;

use App\Models\LabTest;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class LabTestFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var LabTest $model */
		$model = LabTest::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"organization_id" => $model->organization_id,
			"lab_id" => $model->lab_id,
			"size" => $model->size,
			"path" => $model->path,
			"name" => $model->name,
			"display_name" => $model->display_name,
			"result_series_id" => $model->result_series_id,
			"removed" => $model->removed,
		]);
	}
}
