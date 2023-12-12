<?php

namespace Tests\Feature\Factories\LabTestFolder;

use App\Models\LabTestFolder;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class LabTestFolderFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var LabTestFolder $model */
		$model = LabTestFolder::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"organization_id" => $model->organization_id,
			"added_by" => $model->added_by,
			"desc" => $model->desc,
			"title" => $model->title,
			"removed" => $model->removed,
		]);
	}
}
