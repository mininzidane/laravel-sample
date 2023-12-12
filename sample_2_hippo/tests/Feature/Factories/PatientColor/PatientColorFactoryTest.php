<?php

namespace Tests\Feature\Factories\PatientColor;

use App\Models\PatientColor;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientColorFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var PatientColor $model */
		$model = PatientColor::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"color" => $model->color,
		]);
	}
}
