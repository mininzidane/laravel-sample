<?php

namespace Tests\Feature\Factories\PatientAllergy;

use App\Models\PatientAllergy;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientAllergyFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var PatientAllergy $model */
		$model = PatientAllergy::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"allergy" => $model->allergy,
			"removed" => $model->removed,
		]);
	}
}
