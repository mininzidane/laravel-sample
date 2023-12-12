<?php

namespace Tests\Feature\Factories\PatientAlert;

use App\Models\PatientAlert;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientAlertFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var PatientAlert $model */
		$model = PatientAlert::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"organization_id" => $model->organization_id,
			"added_by" => $model->added_by,
			"description" => $model->description,
			"current" => $model->current,
			"removed" => $model->removed,
		]);
	}
}
