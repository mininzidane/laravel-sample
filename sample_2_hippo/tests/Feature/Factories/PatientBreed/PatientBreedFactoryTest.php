<?php

namespace Tests\Feature\Factories\PatientBreed;

use App\Models\PatientBreed;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientBreedFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var PatientBreed $model */
		$model = PatientBreed::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"client_id" => $model->client_id,
			"breed" => $model->breed,
		]);
	}
}
